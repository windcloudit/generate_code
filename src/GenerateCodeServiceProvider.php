<?php

/**
 * This file is part of generate-code
 *
 * (c) Tat Pham <tat.pham89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WindCloud\GenerateCode;

use Illuminate\Support\ServiceProvider;
use WindCloud\GenerateCode\Commands\GenerateAction;
use WindCloud\GenerateCode\Commands\GenerateModel;
use WindCloud\GenerateCode\Commands\GenerateService;

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

        $this->app->singleton('command.generate-service', function ($app) {
            return new GenerateService($app['config']);
        });

        $this->app->singleton('command.generate-action', function ($app) {
            return new GenerateAction($app['config']);
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
