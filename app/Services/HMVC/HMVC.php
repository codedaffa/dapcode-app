<?php

namespace App\Services\HMVC;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ReflectionMethod;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HMVC
{
    /**
     * Dispatch an incoming HTTP request to the target module controller & action.
     *
     * @param Request $request
     * @param string $module
     * @param string|null $segment2
     * @param string|null $segment3
     * @param string|null $params
     * @return mixed
     */
    public function dispatch(Request $request, string $module, ?string $segment2 = null, ?string $segment3 = null, ?string $params = null)
    {
        $moduleName = Str::studly($module);
        $moduleNamespace = "App\\Modules\\{$moduleName}\\Controllers";

        $controllerName = null;
        $actionName = 'index';
        $paramList = [];

        if (!is_null($params) && $params !== '') {
            $paramList = explode('/', trim($params, '/'));
        }

        if ($segment2 === null) {
            // Pattern: /{module}
            $controllerName = "{$moduleName}Controller";
            $actionName = 'index';
        } elseif ($segment3 === null) {
            // Pattern: /{module}/{segment2}
            $candidateSubController = Str::studly($segment2) . 'Controller';
            if (class_exists("{$moduleNamespace}\\{$candidateSubController}")) {
                $controllerName = $candidateSubController;
                $actionName = 'index';
            } else {
                $controllerName = "{$moduleName}Controller";
                $actionName = Str::camel($segment2);
            }
        } else {
            // Pattern: /{module}/{segment2}/{segment3}/{params?}
            $candidateSubController = Str::studly($segment2) . 'Controller';
            if (class_exists("{$moduleNamespace}\\{$candidateSubController}")) {
                $controllerName = $candidateSubController;
                $actionName = Str::camel($segment3);
            } else {
                $controllerName = "{$moduleName}Controller";
                $actionName = Str::camel($segment2);
                array_unshift($paramList, $segment3);
            }
        }

        $fullControllerClass = "{$moduleNamespace}\\{$controllerName}";

        if (!class_exists($fullControllerClass)) {
            throw new NotFoundHttpException("HMVC Module controller [{$fullControllerClass}] not found.");
        }

        $controllerInstance = app()->make($fullControllerClass);

        if (!method_exists($controllerInstance, $actionName)) {
            // Try HTTP method prefix like getIndex, postStore, etc.
            $httpMethodAction = strtolower($request->method()) . ucfirst($actionName);
            if (method_exists($controllerInstance, $httpMethodAction)) {
                $actionName = $httpMethodAction;
            } else {
                throw new NotFoundHttpException("HMVC Action [{$actionName}] not found in [{$fullControllerClass}].");
            }
        }

        return $this->callActionWithResolvedParams($controllerInstance, $actionName, $paramList, $request);
    }

    /**
     * Resolve method parameters combining DI services and URL parameters.
     *
     * @param object $controllerInstance
     * @param string $actionName
     * @param array $paramList
     * @param Request|null $request
     * @return mixed
     */
    protected function callActionWithResolvedParams($controllerInstance, string $actionName, array $paramList, ?Request $request = null)
    {
        $refMethod = new ReflectionMethod($controllerInstance, $actionName);
        $resolved = [];
        $urlParamIndex = 0;

        foreach ($refMethod->getParameters() as $param) {
            $paramType = $param->getType();
            $paramClass = null;

            if ($paramType && !$paramType->isBuiltin()) {
                $paramClass = $paramType->getName();
            }

            if ($paramClass) {
                if ($request && ($paramClass === Request::class || is_subclass_of($paramClass, Request::class))) {
                    $resolved[$param->getName()] = $request;
                } else {
                    $resolved[$param->getName()] = app()->make($paramClass);
                }
            } else {
                if (isset($paramList[$urlParamIndex])) {
                    $resolved[$param->getName()] = $paramList[$urlParamIndex];
                    $urlParamIndex++;
                } elseif ($param->isDefaultValueAvailable()) {
                    $resolved[$param->getName()] = $param->getDefaultValue();
                } else {
                    $resolved[$param->getName()] = null;
                }
            }
        }

        return app()->call([$controllerInstance, $actionName], $resolved);
    }

    /**
     * Run a module controller action hierarchically (HMVC sub-request).
     * Usage: HMVC::run('User/ProfileController@getStats', ['userId' => 1])
     * Or:    HMVC::run('User@getStats', ['userId' => 1])
     *
     * @param string $target
     * @param array $parameters
     * @return mixed
     */
    public static function run(string $target, array $parameters = [])
    {
        // Parse "Module/Controller@action" or "Module@action"
        if (strpos($target, '@') === false) {
            $target .= '@index';
        }

        [$classTarget, $action] = explode('@', $target);
        $segments = explode('/', $classTarget);

        $moduleName = Str::studly($segments[0]);

        if (isset($segments[1])) {
            $controllerName = Str::studly($segments[1]);
            if (!Str::endsWith($controllerName, 'Controller')) {
                $controllerName .= 'Controller';
            }
        } else {
            $controllerName = "{$moduleName}Controller";
        }

        $fullControllerClass = "App\\Modules\\{$moduleName}\\Controllers\\{$controllerName}";

        if (!class_exists($fullControllerClass)) {
            throw new NotFoundHttpException("HMVC Hierarchical controller [{$fullControllerClass}] not found.");
        }

        $controllerInstance = app()->make($fullControllerClass);

        if (!method_exists($controllerInstance, $action)) {
            throw new NotFoundHttpException("HMVC Action [{$action}] not found in [{$fullControllerClass}].");
        }

        return app()->call([$controllerInstance, $action], $parameters);
    }
}
