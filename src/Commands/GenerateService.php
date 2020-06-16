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

use Illuminate\Console\Command;

class GenerateService extends Command
{
    use Builder;

    const REPOSITORY = 'Repository';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description generate service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
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
            $serviceType = $this->choice('Please select your service type?', ['Normal service', 'CRUD master data service']);
            if ($serviceType === 'Normal service') {
                $repository = null;
                $serviceName = $this->ask('Service name: ');
            } else {
                // Input controller info
                // Get list folder controller
                $arrayControllers = self::getListDir(app_path('Http/Controllers'));
                $controllerFolder = $this->anticipate('Controller folder: ', $arrayControllers);
                $isPublishController = $this->confirm('This is publish controller?');
                // Input service info
                $serviceName = $this->ask('Service name: ');
                $arrayRepositories = self::getListDir(app_path('Repositories'));
                $repository = $this->choice('Repository name: ', $arrayRepositories);
                $prefixRoute = $this->ask('Prefix of route');
                $controllerName = explode(self::REPOSITORY, $repository)[0];
                // Generate controller
                $generateController = new ControllerGenerator();
                $generateController->generateController($controllerFolder, $controllerName, $serviceName, $isPublishController);

                // Method for CRUD action
                $arrMethod = ['post', 'get', 'put', 'delete'];
                $arrAction = ['create', 'get', 'update', 'delete'];
                $arrUrl = ['/', '/{id}', '/{id}', '/{id}'];
                $generateRouter = new RouterGenerator();
                $generateTestCase = new TestCaseGenerator();

                foreach ($arrMethod as $key => $method) {
                    $generateRouter->generateRouter(
                        $prefixRoute,
                        $method,
                        "$controllerFolder\\$controllerName".'Controller',
                        $arrAction[$key].$controllerName,
                        $arrUrl[$key]
                    );
                    $generateTestCase->generateTestCase(
                        $controllerFolder,
                        $controllerName,
                        $arrAction[$key].$controllerName,
                        $method,
                        $generateTestCase::TEST_CONTROLLER,
                        'web.'.$prefixRoute.'.'.$arrAction[$key].$controllerName
                    );
                }
            }
            $generateService = new ServiceGenerator();
            $generateService->generateService($serviceName, $repository);
            $this->info('Generate service is finish');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
