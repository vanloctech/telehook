<?php

namespace Vanloctech\Telehook;

use Illuminate\Support\ServiceProvider;
use Vanloctech\Telehook\Console\Commands\ClearTelehookConversationCommand;
use Vanloctech\Telehook\Console\Commands\SetMenuTelegramCommand;
use Vanloctech\Telehook\Console\Commands\SetWebhookCommand;
use Vanloctech\Telehook\Console\Commands\StopTelehookConversationCommand;
use Vanloctech\Telehook\Console\Commands\TelegramCommandMakeCommand;

class TelehookServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerPublishing();
        $this->registerTranslation();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/telehook.php', 'telehook'
        );
        $this->registerCommands();

        $this->app->singleton('telehook', function () {
            return new Telehook();
        });
    }

    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(realpath(__DIR__ . '/Http/routes/telehook-routes.php'));
    }

    /**
     * Register the package's migrations.
     *
     * @return void
     */
    private function registerMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
        }
    }

    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TelegramCommandMakeCommand::class,
                SetMenuTelegramCommand::class,
                SetWebhookCommand::class,
                StopTelehookConversationCommand::class,
                ClearTelehookConversationCommand::class,
            ]);
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/Database/migrations' => database_path('migrations'),
            ], 'telehook-migrations');

            $this->publishes([
                __DIR__.'/../config/telehook.php' => config_path('telehook.php'),
            ], 'telehook-config');

            $this->publishes([
                __DIR__.'/../Resources/lang' => resource_path('lang/vendor/telehook'),
            ]);
        }
    }

    /**
     * @return void
     */
    private function registerTranslation()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'telehook');
    }
}
