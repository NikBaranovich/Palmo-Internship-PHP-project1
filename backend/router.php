<?php

/**
 * Holds the registered routes
 *
 * @var array $routes
 */
$routes = [];

/**
 * Register a new route
 *
 * @param $action string
 * @param \Closure $callback Called when current URL matches provided action
 * @param int $maxParameters Maximum allowed number of dynamic parameters for this route
 */
function route($action, Closure $callback)
{
    global $routes;
    $action = trim($action, '/');
    $routes[$action] = $callback;
}

/**
 * Dispatch the router
 *
 * @param $action string
 */
function dispatch($action)
{
    global $routes;

    $parts = explode('?', $action, 2);
    $path = trim($parts[0], '/');
    $query = isset($parts[1]) ? $parts[1] : '';

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
            if ($routePart !== $pathParts[$index] && strpos($routePart, ':') !== 0) {
                $match = false;
                break;
            }
        }

        if ($match) {
            $callback = $routeCallback;

            parse_str($query, $queryArray);
            $query = $queryArray;

            break;
        }
    }

    if (!$callback) {
        require "./pages/notFound.php";
    } else {
        call_user_func($callback, ...[$pathParts, $query]);
    }
}