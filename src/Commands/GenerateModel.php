<?php

namespace WindCloud\GenerateCode;

use WindCloud\GenerateCode\GenerateModel\ModelGenerator;
use WindCloud\GenerateCode\GenerateRepository\RepositoryGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

/*
 * Class use for generate model getter setter method
 * @author: tat.pham
 */
class GenerateModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description;
    private $config;

    public function __construct($config)
    {
        $this->description = 'Command use for generate model';
        $this->config = $config;
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @author: tat.pham
     *
     * @return mixed
     */
    public function handle()
    {
        $arrTableModelMapping = $this->config->get('generate-model.models');

        // Array need make constant in model format is $tableName => $columnName
        $arrMakeConstant = $this->config->get('generate-model.constants');
        // Generate model
        $modelGenerator = new ModelGenerator();
        $modelGenerator->generateModel($arrTableModelMapping, $arrMakeConstant);
        // Generate repository
        $repositoryGenerator = new RepositoryGenerator();
        $repositoryGenerator->generateRepository($arrTableModelMapping);
    }
}
