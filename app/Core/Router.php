<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    /** @var array<int, array{method: string, path: string, handler: mixed, middleware: array}> */
    private array $routes = [];

    private string $groupPrefix = '';
    private array $groupMiddleware = [];

    public function get(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function group(string $prefix, array $middleware, callable $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->groupPrefix .= $prefix;
        $this->groupMiddleware = array_merge($this->groupMiddleware, $middleware);

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    private function add(string $method, string $path, array|callable $handler, array $middleware): void
    {
        $fullPath = $this->groupPrefix . $path;
        if ($fullPath !== '/' && str_ends_with($fullPath, '/')) {
            $fullPath = rtrim($fullPath, '/');
        }

        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath === '' ? '/' : $fullPath,
            'handler' => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['path'], $uri);
            if ($params === null) {
                continue;
            }

            $handler = $this->resolveHandler($route['handler'], $params);
            $this->runMiddleware($route['middleware'], $handler);
            return;
        }

        http_response_code(404);
        View::render('web/errors/404', ['title' => 'Page Not Found']);
    }

    private function match(string $pattern, string $uri): ?array
    {
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    private function resolveHandler(array|callable $handler, array $params): callable
    {
        if (is_callable($handler)) {
            return fn () => $handler(...array_values($params));
        }

        [$class, $method] = $handler;
        $args = $this->resolveActionArguments($class, $method, $params);

        return function () use ($class, $method, $args) {
            $controller = new $class();
            return $controller->$method(...$args);
        };
    }

    /** @return list<mixed> */
    private function resolveActionArguments(string $class, string $method, array $params): array
    {
        if (!method_exists($class, $method)) {
            return array_values($params);
        }

        $reflection = new \ReflectionMethod($class, $method);
        $args = [];

        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $params)) {
                $args[] = $this->castRouteParam($params[$name], $parameter);
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            }
        }

        if ($args === [] && $params !== []) {
            return array_values($params);
        }

        return $args;
    }

    private function castRouteParam(mixed $value, \ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if (!$type instanceof \ReflectionNamedType || !$type->isBuiltin()) {
            return $value;
        }

        return match ($type->getName()) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $value,
            'string' => (string) $value,
            default => $value,
        };
    }

    private function runMiddleware(array $middleware, callable $handler): void
    {
        $runner = array_reduce(
            array_reverse($middleware),
            function ($next, $name) {
                return function () use ($name, $next) {
                    $class = "App\\Middleware\\{$name}Middleware";
                    if (!class_exists($class)) {
                        $class = "App\\Middleware\\{$name}";
                    }
                    $mw = new $class();
                    return $mw->handle($next);
                };
            },
            $handler
        );

        $runner();
    }
}
