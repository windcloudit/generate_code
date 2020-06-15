<?php

namespace WindCloud\GenerateCode;

use Illuminate\Support\ServiceProvider;

class GenerateCodeServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/generate-model.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('generate-model.php');
        } else {
            $publishPath = base_path('config/generate-model.php');
        }

        $this->publishes([$configPath => $publishPath], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/generate-model.php';
        $this->mergeConfigFrom($configPath, 'generate-model');

        $this->app->singleton('command.generate-model', function ($app) {
            return new GenerateModel($app['config']);
        });

        $this->app->singleton('command.generate-service', function () {
            return new GenerateService();
        });

        $this->app->singleton('command.generate-action', function () {
            return new GenerateAction();
        });

        $this->commands('command.generate-model', 'command.generate-service', 'command.generate-action');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('command.generate-model', 'command.generate-service', 'command.generate-action');
    }
}
