<?php

$routes = [];

function route($action, Closure $callback)
{
    global $routes;
    $action = trim($action, '/');
    $routes[$action] = $callback;
}

function dispatch($action)
{
    global $routes;

    $parts = explode('?', $action, 2);
    $path = trim($parts[0], '/');

    $callback = null;
    $params = [];

    foreach ($routes as $route => $routeCallback) {
        $routeParts = explode('/', $route);
        $pathParts = explode('/', $path);

        if (count($routeParts) !== count($pathParts)) {
            continue;
        }

        $match = true;
        foreach ($routeParts as $index => $routePart) {
            if ((!empty($routePart)) && $routePart[0] === ":") {
                $params[ltrim($routePart, ':')] = $pathParts[$index];
            }
            if ($routePart !== $pathParts[$index] && strpos($routePart, ':') !== 0) {
                $match = false;

                break;
            }
        }

        if ($match) {
            $callback = $routeCallback;

            break;
        }
    }

    if (!$callback) {
        header("Location: /404");
    } else {
        call_user_func($callback, ...[$params]);
    }
}
