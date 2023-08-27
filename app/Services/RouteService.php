<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class RouteService
{
    /**
     *
     */
    public function getActiveForTab($name)
    {
        if (Route::is("{$name}")) {
            return 'active';
        } else {
            return 'text-dark';
        }
    }

    public function getShopIdFromUrl(): string
    {
        $url = url()->current();
        $parts = parse_url($url);
        $path = trim($parts['path'], '/');
        $segments = explode('/', $path);

        return end($segments);
    }
}

?>
