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

use WindCloud\GenerateCode\Commands\ModelGenerator;
use WindCloud\GenerateCode\Commands\RepositoryGenerator;
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
        $modelGenerator->generateModel($arrTableModelMapping, $arrMakeConstant, $this->config);
        // Generate repository
        $repositoryGenerator = new RepositoryGenerator();
        $repositoryGenerator->generateRepository($arrTableModelMapping, $this->config);
    }
}
