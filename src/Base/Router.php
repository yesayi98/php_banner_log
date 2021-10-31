<?php

namespace Base;

class Router
{
    /**
     * @var array
     */
    private array $registeredRoutes;

    /**
     * @var App
     */
    private App $app;

    public function __construct(App $app, array $routes = [])
    {
        $this->app = $app;
        $this->registeredRoutes = $routes;
    }

    /**
     * @return mixed
     */
    public function dispatch()
    {
        $request = $this->app->get('request');
        $uri = $request->getUri();
        $method = $request->getMethod();

        $route = $this->registeredRoutes[$uri . ':' . $method];

        $controllerInstance = new $route['controller'];

        $response = $controllerInstance->{$route['action']}($request);

        $this->app::register('response', $response);

        return $response::send();
    }
}