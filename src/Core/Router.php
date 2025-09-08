<?php

namespace Core;

use Core\DI\DIContainer;
use Exception;

class Router
{
    private $routes = [];

    public function __construct($container = null)
    {
        $this->container = $container ?: DIContainer::instance();
    }

    public function addRoute($method, $path, $handler, $protected, $redirect = null)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'protected' => $protected,
            'redirect' => $redirect
        ];
        return $this;
    }

    public function getRoutes()
    {
        $routes = [];
        foreach ($this->routes as $index => $route) {
            array_push($routes, ["method" => $route['method'], "path" => $route["path"]]);
        }
        return json_encode($routes);
    }

    public function get($path, $handler, $protected)
    {
        return $this->addRoute('GET', $path, $handler, $protected);
    }

    public function post($path, $handler, $protected, $redirect)
    {
        return $this->addRoute('POST', $path, $handler, $protected, $redirect);
    }

    public function put($path, $handler, $protected)
    {
        return $this->addRoute('PUT', $path, $handler, $protected);
    }

    public function delete($path, $handler, $protected, $redirect)
    {
        return $this->addRoute('DELETE', $path, $handler, $protected);
    }

    public function dispatch($method, $uri)
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        error_log("Router DEBUG: Method = $method, URI = $uri");

        error_log("DISPATCH: uri = $uri");

        foreach ($this->routes as $index => $route) {

            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                return $this->callHandler($route['handler']);
            }
        }

        error_log("Router DEBUG: No route found for $method $uri");
        http_response_code(404);
        return json_encode(['error' => 'Route not found', 'debug' => ['method' => $method, 'uri' => $uri]]);
    }

    private function callHandler($handler, $params = [])
    {
        if (is_array($handler) && count($handler) === 2 && is_object($handler[0])) {
            list($instance, $method) = $handler;

            if (!method_exists($instance, $method)) {
                $className = get_class($instance);
                throw new Exception("Method {$method} not found in {$className}");
            }

            return call_user_func_array([$instance, $method], $params);
        }

        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler)) {
            return $handler;
        }

        throw new Exception("Invalid handler type: " . gettype($handler));
    }

    private function matchPath($routePath, $uri)
    {
        return $routePath === $uri ||
            (strpos($routePath, ':') !== false && $this->matchParams($routePath, $uri));
    }

    private function matchParams($routePath, $uri)
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        error_log("matchParams: route=$routePath, uri=$uri, parts=" . json_encode($uriParts));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        for ($i = 0; $i < count($routeParts); $i++) {
            if (strpos($routeParts[$i], ':') === 0) {
                $paramName = substr($routeParts[$i], 1);
                $_GET[$paramName] = $uriParts[$i];
            } elseif ($routeParts[$i] !== $uriParts[$i]) {
                return false;
            }
        }

        return true;
    }
}
