<?php

namespace Lib\Router;

use Lib\Exceptions\NotFoundException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;

class Router 
{
    private array $routesCollection;
    
    public function __construct()
    {
        // Get route collection from Yaml file
        $routesCollection = [];
        $routes = Yaml::parseFile('../config/routes.yaml');

        foreach($routes as $routesNames => $oneRoute)
        {
            $route = new Route($routesNames, $oneRoute['path'], $oneRoute['controller']);
            $routesCollection[$routesNames] = $route;
        }

        $this->routesCollection = $routesCollection;
    }

    /**
     * @param Request $request
     * @return Route|null
     * @throws NotFoundException
     */
    public function getRouteFromRequest(Request $request) : ?Route
    {
        foreach ($this->routesCollection as $route)
        {
            if($route->checkMatch($request))
            {
                return $route;
            }
        }
        throw new NotFoundException('Page introuvable', 404);
    }
}