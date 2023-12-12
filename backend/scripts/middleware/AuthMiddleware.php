<?php
namespace Palmo\Core\middleware;

class AuthMiddleware
{

    public static function handle($location)
    {
        if (!isAuthorized()) {
            header("Location: $location");
            return;
        }
    }
}
