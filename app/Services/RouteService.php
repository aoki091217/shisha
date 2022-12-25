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
}

?>
