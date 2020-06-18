<?php

/**
 * This file is part of generate-code
 *
 * (c) Tat Pham <tat.pham89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WindCloud\GenerateCode\Commands;

use WindCloud\GenerateCode\Commands\GenerateController\ControllerGenerator;
use Illuminate\Console\Command;

class GenerateAction extends Command
{
    use Builder;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command generate a action for controller';
    private $config;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($config)
    {
        $this->config = $config;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle()
    {
        try {
            $arrayControllersFolder = self::getListDir(app_path('Http/Controllers'));
            $controllerFolder = $this->choice('Controller folder: ', $arrayControllersFolder);
            $arrayControllers = self::getListDir(app_path("Http/Controllers/$controllerFolder"), true);
            $controllerFile = $this->choice('Controller folder file: ', $arrayControllers);
            $method = $this->choice('Method: ', ['get', 'post', 'put', 'delete']);
            $actionName = $this->ask('Action name: ');
            $actionType = $this->choice('Action type: ', ['web', 'api'], 'web');
            $prefixRoute = $this->ask('Prefix route: ');
            $url = $this->ask('Url: ');

            $generateController = new ControllerGenerator();
            $generateController->generateAction($controllerFolder, $controllerFile, $method, $actionName, $prefixRoute, $url, $actionType, $this->config);

            $this->info('Generate action is finish');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
