<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Validator::extend('check_array', function ($attribute, $value, $parameters, $validator) {
            return count(array_filter($value, function($var) use ($parameters) { return ( $var && $var >= $parameters[0]); }));
        });
    }
}
