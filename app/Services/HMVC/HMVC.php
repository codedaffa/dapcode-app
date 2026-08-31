<?php

namespace App\Services\HMVC;

use App\Services\Dapcode\LicenseGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ReflectionMethod;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HMVC
{
    /**
     * Stack to track active modules during hierarchical dispatching.
     *
     * @var array<int, string>
     */
    protected static $activeModuleStack = [];

    /**
     * Push active module onto the dispatch stack.
     *
     * @param string $module
     * @return void
     */
    public static function pushActiveModule(string $module): void
    {
        static::$activeModuleStack[] = Str::studly($module);
    }

    /**
     * Pop active module from the dispatch stack.
     *
     * @return void
     */
    public static function popActiveModule(): void
    {
        array_pop(static::$activeModuleStack);
    }

    /**
     * Get the currently active module on top of the stack.
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
        $activeModule = static::getActiveModule();
        if ($activeModule && (!$excludeModule || strcasecmp($activeModule, $excludeModule) !== 0)) {
            return $activeModule;
        }

        return null;
    }

    /**
     * Enforce module isolation: Prevent cross-module direct calls.
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
            // Cross-module call detected! Redirect to portfolio root (/)
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect('/')
            );
        }
    }

    /**
     * Canonical module name resolver (Layer 2 Security Boundary).
     * Normalizes case, trims slashes, rejects traversal attempts and invalid characters.
     *
     * @param string|null $rawModule
     * @return string|null Normalized lowercase module name or null if invalid
     */
    public static function resolveCanonicalModuleName(?string $rawModule): ?string
    {
        if ($rawModule === null || $rawModule === '') {
            return null;
        }

        // 1. URL decode recursively to prevent double-encoding bypasses
        $decoded = rawurldecode(urldecode($rawModule));

        // 2. Reject path traversal sequences
        if (strpos($decoded, '..') !== false || strpos($decoded, '%2e%2e') !== false) {
            return null;
        }

        // 3. Clean leading/trailing slashes and directory separators
        $clean = trim($decoded, "/\\ \t\n\r\0\x0B");

        // 4. Extract first path segment if multiple segments were provided
        if (strpos($clean, '/') !== false) {
            $clean = explode('/', $clean)[0];
        }
        if (strpos($clean, '\\') !== false) {
            $clean = explode('\\', $clean)[0];
        }

        // 5. Sanitize to strict alphanumeric, underscores, and hyphens only
        $clean = preg_replace('/[^A-Za-z0-9_\-]/', '', $clean);

        if (empty($clean)) {
            return null;
        }

        return strtolower($clean);
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
    public function dispatch(
        Request $request,
        string $module,
        ?string $segment2 = null,
        ?string $segment3 = null,
        ?string $params = null
    ) {
        $canonicalModule = static::resolveCanonicalModuleName($module);
        if ($canonicalModule === null) {
            throw new NotFoundHttpException("Invalid module identifier.");
        }

        // Layer 2 Defense: Central Execution Boundary — Enforce module license authorization before controller instantiation
        LicenseGuard::assertModuleAllowed($canonicalModule);

        $moduleName = Str::studly($canonicalModule);
        $moduleNamespace = "App\\Modules\\{$moduleName}\\Controllers";

        $urlParameters = !is_null($params) && $params !== ''
            ? explode('/', trim($params, '/'))
            : [];

        [$controllerClass, $actionName, $urlParameters] = $this->resolveDispatchTarget(
            $moduleNamespace,
            $moduleName,
            $segment2,
            $segment3,
            $urlParameters
        );

        if (!class_exists($controllerClass)) {
            throw new NotFoundHttpException("HMVC Module controller [{$controllerClass}] not found.");
        }

        static::pushActiveModule($moduleName);

        try {
            $controllerInstance = app()->make($controllerClass);
            $finalAction = $this->resolveActionMethod($controllerInstance, $actionName, $request->method());

            return $this->callActionWithResolvedParams($controllerInstance, $finalAction, $urlParameters, $request);
        } finally {
            static::popActiveModule();
        }
    }

    /**
     * Resolve the target controller class and action name based on URL segments.
     *
     * @param string $namespace
     * @param string $module
     * @param string|null $segment2
     * @param string|null $segment3
     * @param array $parameters
     * @return array{0: string, 1: string, 2: array} [ControllerClass, ActionName, Parameters]
     */
    protected function resolveDispatchTarget(
        string $namespace,
        string $module,
        ?string $segment2,
        ?string $segment3,
        array $parameters
    ): array {
        $mainController = $this->resolveControllerClass($namespace, $module);

        // Pattern 1: /{module}
        if ($segment2 === null) {
            return [$mainController, 'index', $parameters];
        }

        $subCandidate = Str::studly($segment2);
        $subController = $this->resolveControllerClass($namespace, $subCandidate);

        // Pattern 2: /{module}/{segment2}
        if ($segment3 === null) {
            if ($subController !== null) {
                return [$subController, 'index', $parameters];
            }

            return [$mainController, Str::camel($segment2), $parameters];
        }

        // Pattern 3: /{module}/{segment2}/{segment3}/{params?}
        if ($subController !== null) {
            return [$subController, Str::camel($segment3), $parameters];
        }

        array_unshift($parameters, $segment3);
        return [$mainController, Str::camel($segment2), $parameters];
    }

    /**
     * Find existing controller class by trying candidate names with and without 'Controller' suffix.
     *
     * @param string $namespace
     * @param string $candidateName
     * @return string|null
     */
    protected function resolveControllerClass(string $namespace, string $candidateName): ?string
    {
        $exactClass = "{$namespace}\\{$candidateName}";
        if (class_exists($exactClass)) {
            return $exactClass;
        }

        $suffixedClass = "{$namespace}\\{$candidateName}Controller";
        if (class_exists($suffixedClass)) {
            return $suffixedClass;
        }

        return null;
    }

    /**
     * Resolve callable action method name, supporting HTTP method prefixes (e.g. getIndex, postStore).
     *
     * @param object $controllerInstance
     * @param string $actionName
     * @param string $httpMethod
     * @return string
     */
    protected function resolveActionMethod($controllerInstance, string $actionName, string $httpMethod): string
    {
        if (method_exists($controllerInstance, $actionName)) {
            return $actionName;
        }

        $prefixedAction = strtolower($httpMethod) . ucfirst($actionName);
        if (method_exists($controllerInstance, $prefixedAction)) {
            return $prefixedAction;
        }

        $controllerClass = get_class($controllerInstance);
        throw new NotFoundHttpException("HMVC Action [{$actionName}] not found in [{$controllerClass}].");
    }

    /**
     * Resolve method parameters combining Dependency Injection services and URL parameters.
     *
     * @param object $controllerInstance
     * @param string $actionName
     * @param array $paramList
     * @param Request|null $request
     * @return mixed
     */
    protected function callActionWithResolvedParams(
        $controllerInstance,
        string $actionName,
        array $paramList,
        ?Request $request = null
    ) {
        $refMethod = new ReflectionMethod($controllerInstance, $actionName);
        $resolvedArguments = [];
        $urlParamIndex = 0;

        foreach ($refMethod->getParameters() as $param) {
            $paramName = $param->getName();
            $paramType = $param->getType();
            $paramClassName = ($paramType && !$paramType->isBuiltin()) ? $paramType->getName() : null;

            if ($paramClassName !== null) {
                // Dependency Injection parameter
                if ($request && ($paramClassName === Request::class || is_subclass_of($paramClassName, Request::class))) {
                    $resolvedArguments[$paramName] = $request;
                } else {
                    $resolvedArguments[$paramName] = app()->make($paramClassName);
                }
            } else {
                // URL positional parameter
                if (isset($paramList[$urlParamIndex])) {
                    $resolvedArguments[$paramName] = $paramList[$urlParamIndex];
                    $urlParamIndex++;
                } elseif ($param->isDefaultValueAvailable()) {
                    $resolvedArguments[$paramName] = $param->getDefaultValue();
                } else {
                    $resolvedArguments[$paramName] = null;
                }
            }
        }

        return app()->call([$controllerInstance, $actionName], $resolvedArguments);
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
        if (strpos($target, '@') === false) {
            $target .= '@index';
        }

        [$classTarget, $action] = explode('@', $target);
        $segments = explode('/', $classTarget);

        $canonicalModule = static::resolveCanonicalModuleName($segments[0]);
        if ($canonicalModule === null) {
            throw new NotFoundHttpException("Invalid module identifier in HMVC hierarchical call.");
        }

        // Layer 2 Defense: Central Execution Boundary — Enforce module license authorization for hierarchical HMVC calls
        LicenseGuard::assertModuleAllowed($canonicalModule);

        $moduleName = Str::studly($canonicalModule);
        $moduleNamespace = "App\\Modules\\{$moduleName}\\Controllers";

        // Enforce module isolation
        static::enforceNoCrossModule($moduleName);

        $controllerCandidate = isset($segments[1]) ? Str::studly($segments[1]) : $moduleName;
        $controllerClass = "{$moduleNamespace}\\{$controllerCandidate}";

        if (!class_exists($controllerClass)) {
            $controllerClass = "{$moduleNamespace}\\{$controllerCandidate}Controller";
        }

        if (!class_exists($controllerClass)) {
            throw new NotFoundHttpException("HMVC Hierarchical controller [{$controllerClass}] not found.");
        }

        static::pushActiveModule($moduleName);

        try {
            $controllerInstance = app()->make($controllerClass);

            if (!method_exists($controllerInstance, $action)) {
                throw new NotFoundHttpException("HMVC Action [{$action}] not found in [{$controllerClass}].");
            }

            return app()->call([$controllerInstance, $action], $parameters);
        } finally {
            static::popActiveModule();
        }
    }
}
