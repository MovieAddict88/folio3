<?php

namespace App\Core;

class Router {
    protected $routes = [];

    public function add($uri, $controller, $method) {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method,
        ];
    }

    public function dispatch($uri) {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri) {
                $controller = new $route['controller']();
                $method = $route['method'];
                $controller->$method();
                return;
            }
        }

        // Handle 404
        http_response_code(404);
        echo '404 Not Found';
    }
}
?>