<?php
namespace Palmo\Core\middleware;

class GuestMiddleware
{

    public static function handle($location)
    {
        if (isAuthorized()) {
            header("Location: $location");
            return;
        }
    }
}