<?php

declare(strict_types=1);

namespace Loginner\FactorySpace;

class Router
{
    private array $routes;

    public function addRoute(string $method, string $url, callable|array $action): self // : Route //static
    {
        $this->routes[$method][$url] = $action;

        return $this; //new static()
    }

    public function get(string $url, callable|array $action): self
    {
        return $this->addRoute('GET', $url, $action); //self::
    }

    public function post(string $url, callable|array $action): self
    {
        return $this->addRoute('POST', $url, $action);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function resolveRoute(Request $request): Response
    {
        echo __METHOD__ . '<br>';

        $route = $request->getRoute();
        $method = $request->getMethod();
        $parameters = $request->getParameters(); // u response?

        $action = $this->routes[$method][$route] ?? null;

        var_dump($action);

        if (!$action) {
            throw new RouteNotFoundException();
        }

        if (is_callable($action)) {
            return call_user_func($action);
        }

        if (is_array($action)) {
            [$class, $method] = $action;
            if (class_exists($class)) {
                $class = new $class();

                if (method_exists($class, $method)) {
                    var_dump('Route-Method combo exists True', $class, $method);
                    return call_user_func_array([$class, $method], [$parameters]);
                }
            }
        }

        throw new RouteNotFoundException();
        //        return 'Route resolved, here\'s a Response';
        //        return new Response();
    }
}
