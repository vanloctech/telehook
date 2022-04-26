<?php

namespace Vanloctech\Telehook;

use Illuminate\Support\ServiceProvider;
use Vanloctech\Telehook\Console\Commands\SetMenuTelegramCommand;
use Vanloctech\Telehook\Console\Commands\TelegramCommandMakeCommand;
use Vanloctech\Telehook\Console\Commands\SetWebhookCommand;

class TelehookServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfigs();
        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([
                TelegramCommandMakeCommand::class,
                SetMenuTelegramCommand::class,
                SetWebhookCommand::class,
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('telehook', function () {
            return new Telehook();
        });

        $this->mergeConfigFrom(
            __DIR__.'/../config/telehook.php', 'telehook'
        );
    }

    protected function publishConfigs(): void
    {
        $this->publishes([
            __DIR__ . '/../config/telehook.php' => config_path('telehook.php'),
        ]);
    }

    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(realpath(__DIR__ . '/Http/routes/telehook-routes.php'));
    }
}
