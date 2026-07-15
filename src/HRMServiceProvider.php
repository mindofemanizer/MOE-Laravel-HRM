<?php

declare(strict_types=1);

namespace Moe\HRM;

use Illuminate\Support\ServiceProvider;

class HRMServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/hrm.php', 'hrm');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/hrm.php' => config_path('hrm.php'),
        ], 'hrm-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'hrm-migrations');
    }
}
