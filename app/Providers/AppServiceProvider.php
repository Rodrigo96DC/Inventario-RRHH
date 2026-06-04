<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notificacion;


class AppServiceProvider extends ServiceProvider
{
 public function boot(): void
{
    View::composer('*', function ($view) {
        if (auth()->check()) {
            $view->with('notificacionesCount', Notificacion::noLeidas()->count());
            $view->with('notificaciones',      Notificacion::noLeidas()->latest()->take(10)->get());
        }
    });
}
}