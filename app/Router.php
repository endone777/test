<?php

namespace App;

use JetBrains\PhpStorm\NoReturn;

class Router
{
    private array $routes = [];

    #[NoReturn] public function dispatch(): void
    {
        $currentUri = ['uri' => explode('?', $_SERVER['REQUEST_URI'])[0], 'method' => $_SERVER['REQUEST_METHOD']];
        foreach ($this->routes as $k => $route) {
            if ($route['uri'] == $currentUri['uri']) {
                if ($route['method'] == $currentUri['method']) {
                    {
                        $this->route($route['callback'], $route['params']);
                        exit();
                    }
                } else {
                    $this->route('notAllowedMethod');
                }
            }
        }
        $this->route('pageNotFound');
    }

    #[NoReturn] protected function route($action): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo match (true) {
            is_callable($action) => call_user_func_array($action, []),
            is_array($action) => $this->actionIsArray($action),
            is_string($action) => $this->$action(),
            default => $this->pageNotFound()
        };
        exit();
    }

    protected function actionIsArray(array $action): void
    {
        $class = new $action[0]();
        $method = $action[1];
        try {
            echo json_encode($class->$method());
        } catch (\Exception $e) {
            $this->notAllowedMethod();
        }
    }

    public function get($uri, \Closure|array|string $callback): void
    {
        $this->routesPush([
            'uri' => $uri,
            'method' => 'GET',
            'callback' => $callback,
            'params' => count($_GET) ? array_combine(array_keys($_GET), array_values($_GET)) : 0,
        ]);
    }

    public function post($uri, \Closure|array|string $callback): void
    {
        $uri = strtok($uri, '?');
        $this->routesPush([
            'uri' => $uri,
            'method' => 'POST',
            'callback' => $callback,
            'params' => isset($_POST['data']) ? $this->postDataParse($_POST['data']) : 0,
        ]);
    }

    private function routesPush(array $route): void
    {
        $this->routes[] = $route;
    }

    private function postDataParse(string $data): array
    {
        $json = preg_replace('/[[:cntrl:]]/', '', $data);
        $json = json_decode($json, true);
        if($json !== null){
            $data1 = array_combine(array_keys($json), array_values($json));
        }
        return $json;
    }

    #[NoReturn] private function notAllowedMethod(): void
    {
        http_response_code(405);
        echo json_encode([
            'error' => true,
            'message' => 'Method Not Allowed',
        ]);
        exit();
    }

    #[NoReturn] private function pageNotFound(): void
    {
        http_response_code(404);
        echo json_encode([
            'error' => true,
            'message' => 'Page not found',
        ]);
        exit();
    }

}