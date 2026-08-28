<?php

namespace App\Services\HMVC;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ReflectionMethod;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HMVC
{
    /**
     * Stack to track the currently active dispatched modules.
     *
     * @var array
     */
    protected static $activeModuleStack = [];

    /**
     * Push active module onto the stack.
     *
     * @param string $module
     * @return void
     */
    public static function pushActiveModule(string $module): void
    {
        static::$activeModuleStack[] = Str::studly($module);
    }

    /**
     * Pop active module from the stack.
     *
     * @return void
     */
    public static function popActiveModule(): void
    {
        array_pop(static::$activeModuleStack);
    }

    /**
     * Get the currently active module.
     *
     * @return string|null
     */
    public static function getActiveModule(): ?string
    {
        return !empty(static::$activeModuleStack) ? end(static::$activeModuleStack) : null;
    }

    /**
     * Detect the calling module name from the execution backtrace or active stack.
     *
     * @param string|null $excludeModule
     * @return string|null
     */
    public static function detectCallerModule(?string $excludeModule = null): ?string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($trace as $frame) {
            $class = $frame['class'] ?? '';
            $file = isset($frame['file']) ? str_replace('\\', '/', $frame['file']) : '';

            // 1. Check from class namespace: App\Modules\{ModuleName}\...
            if (preg_match('/^App\\\\Modules\\\\([A-Za-z0-9_]+)\\\\/i', $class, $matches)) {
                $module = Str::studly($matches[1]);
                if (!$excludeModule || strcasecmp($module, $excludeModule) !== 0) {
                    return $module;
                }
            }

            // 2. Check from file path: .../app/Modules/{ModuleName}/...
            if (preg_match('#/app/Modules/([A-Za-z0-9_]+)/#i', $file, $matches)) {
                $module = Str::studly($matches[1]);
                if (!$excludeModule || strcasecmp($module, $excludeModule) !== 0) {
                    return $module;
                }
            }
        }

        // 3. Fallback to active dispatch module
        $active = static::getActiveModule();
        if ($active && (!$excludeModule || strcasecmp($active, $excludeModule) !== 0)) {
            return $active;
        }

        return null;
    }

    /**
     * Enforce that cross-module calls are forbidden.
     * If a cross-module call is detected, redirect immediately to the main index page (/).
     *
     * @param string $targetModule
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public static function enforceNoCrossModule(string $targetModule): void
    {
        $targetModule = Str::studly($targetModule);
        $callerModule = static::detectCallerModule($targetModule);

        if ($callerModule !== null && strcasecmp($callerModule, $targetModule) !== 0) {
            // Cross-module call detected! Redirect to main index (/)
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect('/')
            );
        }
    }

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

        $mainController = class_exists("{$moduleNamespace}\\{$moduleName}")
            ? $moduleName
            : "{$moduleName}Controller";

        if ($segment2 === null) {
            // Pattern: /{module}
            $controllerName = $mainController;
            $actionName = 'index';
        } elseif ($segment3 === null) {
            // Pattern: /{module}/{segment2}
            $subStudly = Str::studly($segment2);
            if (class_exists("{$moduleNamespace}\\{$subStudly}")) {
                $controllerName = $subStudly;
                $actionName = 'index';
            } elseif (class_exists("{$moduleNamespace}\\{$subStudly}Controller")) {
                $controllerName = "{$subStudly}Controller";
                $actionName = 'index';
            } else {
                $controllerName = $mainController;
                $actionName = Str::camel($segment2);
            }
        } else {
            // Pattern: /{module}/{segment2}/{segment3}/{params?}
            $subStudly = Str::studly($segment2);
            if (class_exists("{$moduleNamespace}\\{$subStudly}")) {
                $controllerName = $subStudly;
                $actionName = Str::camel($segment3);
            } elseif (class_exists("{$moduleNamespace}\\{$subStudly}Controller")) {
                $controllerName = "{$subStudly}Controller";
                $actionName = Str::camel($segment3);
            } else {
                $controllerName = $mainController;
                $actionName = Str::camel($segment2);
                array_unshift($paramList, $segment3);
            }
        }

        $fullControllerClass = "{$moduleNamespace}\\{$controllerName}";

        if (!class_exists($fullControllerClass)) {
            throw new NotFoundHttpException("HMVC Module controller [{$fullControllerClass}] not found.");
        }

        static::pushActiveModule($moduleName);

        try {
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
        } finally {
            static::popActiveModule();
        }
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
     * Usage: HMVC::run('User/Profile@getStats', ['userId' => 1])
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

        // Enforce no cross-module calls
        static::enforceNoCrossModule($moduleName);

        if (isset($segments[1])) {
            $candidate = Str::studly($segments[1]);
            if (class_exists("App\\Modules\\{$moduleName}\\Controllers\\{$candidate}")) {
                $controllerName = $candidate;
            } elseif (class_exists("App\\Modules\\{$moduleName}\\Controllers\\{$candidate}Controller")) {
                $controllerName = "{$candidate}Controller";
            } else {
                $controllerName = $candidate;
            }
        } else {
            if (class_exists("App\\Modules\\{$moduleName}\\Controllers\\{$moduleName}")) {
                $controllerName = $moduleName;
            } else {
                $controllerName = "{$moduleName}Controller";
            }
        }

        $fullControllerClass = "App\\Modules\\{$moduleName}\\Controllers\\{$controllerName}";

        if (!class_exists($fullControllerClass)) {
            throw new NotFoundHttpException("HMVC Hierarchical controller [{$fullControllerClass}] not found.");
        }

        static::pushActiveModule($moduleName);

        try {
            $controllerInstance = app()->make($fullControllerClass);

            if (!method_exists($controllerInstance, $action)) {
                throw new NotFoundHttpException("HMVC Action [{$action}] not found in [{$fullControllerClass}].");
            }

            return app()->call([$controllerInstance, $action], $parameters);
        } finally {
            static::popActiveModule();
        }
    }
}
