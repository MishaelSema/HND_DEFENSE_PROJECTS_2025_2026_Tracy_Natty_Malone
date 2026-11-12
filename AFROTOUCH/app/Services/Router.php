<?php

namespace App\Services;

class Router
{
    private static array $routes = [];
    private static array $middleware = [];

    public static function get(string $path, $handler, array $middleware = []): void
    {
        self::$routes['GET'][$path] = ['handler' => $handler, 'middleware' => $middleware];
    }

    public static function post(string $path, $handler, array $middleware = []): void
    {
        self::$routes['POST'][$path] = ['handler' => $handler, 'middleware' => $middleware];
    }

    public static function put(string $path, $handler, array $middleware = []): void
    {
        self::$routes['PUT'][$path] = ['handler' => $handler, 'middleware' => $middleware];
    }

    public static function delete(string $path, $handler, array $middleware = []): void
    {
        self::$routes['DELETE'][$path] = ['handler' => $handler, 'middleware' => $middleware];
    }

    public static function addMiddleware(string $name, callable $callback): void
    {
        self::$middleware[$name] = $callback;
    }

    public static function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }
        
        $basePath = '/AFROTOUCH/public';
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
            if ($uri === '') {
                $uri = '/';
            }
        }
        
        $routeKey = self::matchRoute($method, $uri);
        
        if ($routeKey === null) {
            http_response_code(404);
            echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>404</title></head><body style="font-family:Inter,sans-serif;background:#2D1F1A;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh"><div style="text-align:center"><h1 style="font-size:4rem;margin:0">404</h1><p>Page not found</p><a href="/AFROTOUCH/public/" style="color:#F1C40F">Go Home</a></div></body></html>';
            return;
        }

        $route = self::$routes[$method][$routeKey];
        
        $params = self::extractParams($routeKey, $uri);
        if (!empty($params)) {
            $_GET = array_merge($_GET, $params);
        }
        
        foreach ($route['middleware'] as $mw) {
            if (isset(self::$middleware[$mw])) {
                self::$middleware[$mw]();
            }
        }

        $handler = $route['handler'];
        
        if (is_array($handler)) {
            [$controller, $methodName] = $handler;
            $controllerInstance = new $controller();
            
            // Check if method needs parameters
            $reflection = new \ReflectionMethod($controller, $methodName);
            $params = $reflection->getParameters();
            
            if (!empty($params)) {
                $paramValues = [];
                foreach ($params as $param) {
                    $paramName = $param->getName();
                    $paramValues[] = $_GET[$paramName] ?? null;
                }
                $controllerInstance->$methodName(...$paramValues);
            } else {
                $controllerInstance->$methodName();
            }
        } else {
            $handler();
        }
    }
    
    private static function matchRoute(string $method, string $uri): ?string
    {
        if (isset(self::$routes[$method][$uri])) {
            return $uri;
        }
        
        foreach (self::$routes[$method] ?? [] as $routePath => $handler) {
            if (strpos($routePath, '{') !== false) {
                $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
                $pattern = '#^' . $pattern . '$#';
                
                if (preg_match($pattern, $uri, $matches)) {
                    return $routePath;
                }
            }
        }
        
        return null;
    }
    
    private static function extractParams(string $routePath, string $uri): array
    {
        $params = [];
        
        if (strpos($routePath, '{') === false) {
            return $params;
        }
        
        $routeParts = explode('/', $routePath);
        $uriParts = explode('/', $uri);
        
        foreach ($routeParts as $index => $part) {
            if (isset($part[0]) && $part[0] === '{') {
                $paramName = trim($part, '{}');
                if (isset($uriParts[$index])) {
                    $params[$paramName] = $uriParts[$index];
                }
            }
        }
        
        return $params;
    }

    public static function view(string $template, array $data = []): void
    {
        extract($data);
        $title = $title ?? 'AfroTouch';
        
        $isDashboard = strpos($template, 'dashboard/') === 0;
        $isAdmin = strpos($template, 'admin/') === 0;
        
        $baseViewsPath = dirname(__DIR__) . '/Views/';
        $templatePath = $baseViewsPath . $template . '.php';
        
        if (!file_exists($templatePath)) {
            http_response_code(404);
            echo "Template not found: $templatePath";
            return;
        }
        
        if ($isDashboard) {
            include dirname(__DIR__) . '/Views/layouts/dashboard_start.php';
            include $templatePath;
            include dirname(__DIR__) . '/Views/layouts/dashboard_end.php';
        } elseif ($isAdmin) {
            include dirname(__DIR__) . '/Views/layouts/admin_start.php';
            include $templatePath;
            include dirname(__DIR__) . '/Views/layouts/admin_end.php';
        } else {
            $content = '';
            ob_start();
            include $templatePath;
            $content = ob_get_clean();
            
            include dirname(__DIR__) . '/Views/layouts/app.php';
        }
    }

    public static function redirect(string $path): void
    {
        if (strpos($path, '/') === 0 && strpos($path, '/AFROTOUCH') !== 0) {
            $path = '/AFROTOUCH/public' . $path;
        }
        header("Location: $path");
        exit;
    }

    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: $referer");
        exit;
    }

    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
