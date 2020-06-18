<?php

/**
 * This file is part of generate-code
 *
 * (c) Tat Pham <tat.pham89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WindCloud\GenerateCode\Commands\GenerateService;

use WindCloud\GenerateCode\Builder;

class ServiceGenerator
{
    use Builder;

    const SEPARATE_CODE = '//*******************AUTO GENERATED - DO NOT MODIFY FROM HERE******************************';
    const INTERFACE_TEMPLATE = 'interface.txt';
    const INTERFACE_TEMPLATE_CRUD = 'interfaceCRUD.txt';
    const INTERFACE_IML_TEMPLATE = 'interfaceImpl.txt';
    const INTERFACE_IML_TEMPLATE_CURD = 'interfaceImplCRUD.txt';
    const REPOSITORY = 'Repository';

    /**
     * Function use for generate service from name service input
     * @param string $serviceName
     * @param string $repository
     * @return boolean
     * @throws \Exception
     * @author: tat.pham
     */
    public function generateService(string $serviceName, string $repository = null, $config = null): ?bool
    {
        try {
            echo "======START GENERATE SERVICE======\n";
            echo "Create service: $serviceName" . "Service.php' \n";
            $this->createServiceInterface($serviceName, $repository, $config);
            echo "Create service Impl: $serviceName" . "ServiceImpl.php\n";
            $this->createServiceIml($serviceName, $repository, $config);
            //Update bind file
            $result = $this->updateBindFile($serviceName);
            echo "======END GENERATE SERVICE======\n";
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Function use for generate repository interface
     * @param $serviceName
     * @param $repository
     * @return bool
     * @throws \Exception
     * @author: tat.pham
     *
     */
    private function createServiceInterface($serviceName, $repository, $config): bool
    {
        //Model path
        $serviceFolder = app_path(implode(DIRECTORY_SEPARATOR, ['Services', $serviceName . 'Service']));
        //Create folder service if not exist
        $isExist = file_exists($serviceFolder);
        if ($isExist === false) {
            mkdir($serviceFolder, 0755, true);
        } else {
            throw new \Exception('This service already exist');
        }
        //Create repository Interface
        $repositoryInterfacePath = $serviceFolder . DIRECTORY_SEPARATOR . $serviceName . 'Service.php';
        if ($repository === null) {
            $templateRepositoryInterfacePath = __DIR__ . DIRECTORY_SEPARATOR .(implode(DIRECTORY_SEPARATOR, ['Templates', self::INTERFACE_TEMPLATE]));
        } else {
            $templateRepositoryInterfacePath = __DIR__ . DIRECTORY_SEPARATOR .(implode(DIRECTORY_SEPARATOR, ['Templates', self::INTERFACE_TEMPLATE_CRUD]));
        }

        //Get template model
        $templateContentNewRepositoryInterface = file_get_contents($templateRepositoryInterfacePath);
        if ($repository === null) {
            $contentModel = self::bind($templateContentNewRepositoryInterface, array(
                'serviceName' => $serviceName,
                'author' => $config->get('generate-model.author')
            ));
        } else {
            $modelName = explode(self::REPOSITORY, $repository)[0];

            $contentModel = self::bind($templateContentNewRepositoryInterface, array(
                'serviceName' => $serviceName,
                'modelName' => $modelName,
                'modelNameCamel' => strtolower(substr($modelName, 0, 1)) . substr($modelName, 1, strlen($modelName)),
                'author' => $config->get('generate-model.author')
            ));
        }

        file_put_contents($repositoryInterfacePath, $contentModel);
        return true;
    }

    /**
     * Function use for generate repository impl
     * @param $serviceName
     *
     * @param $repository
     * @return bool
     * @author: tat.pham
     */
    private function createServiceIml($serviceName, $repository, $config)
    {
        //Model path
        $repositoryImlPath = app_path(implode(DIRECTORY_SEPARATOR, ['Services', $serviceName . 'Service', $serviceName . 'ServiceImpl.php']));
        //Create repository Interface
        if ($repository === null) {
            $templateRepositoryInterfacePath = __DIR__ . DIRECTORY_SEPARATOR .(implode(                DIRECTORY_SEPARATOR, ['Templates', self::INTERFACE_IML_TEMPLATE]));
        } else {
            $templateRepositoryInterfacePath = __DIR__ . DIRECTORY_SEPARATOR .(implode(DIRECTORY_SEPARATOR, ['Templates', self::INTERFACE_IML_TEMPLATE_CURD]));
        }
        //Get template model
        $templateContentNewRepositoryImpl = file_get_contents($templateRepositoryInterfacePath);
        $contentModel = $templateContentNewRepositoryImpl;
        //Binding data to template service impl
        if ($repository === null) {
            $contentModel = self::bind($contentModel, array(
                'serviceName' => $serviceName,
                'author' => $config->get('generate-model.author')
            ));
        } else {
            $modelName = explode(self::REPOSITORY, $repository)[0];

            $contentModel = self::bind($contentModel, array(
                'serviceName' => $serviceName,
                'modelName' => $modelName,
                'modelNameCamel' => strtolower(substr($modelName, 0, 1)) . substr($modelName, 1, strlen($modelName)),
                'repositoryName' => $repository,
                'repositoryNameCamel' => strtolower(substr($repository, 0, 1)) . substr($repository, 1, strlen($repository)),
                'author' => $config->get('generate-model.author')
            ));
        }
        file_put_contents($repositoryImlPath, $contentModel);
        return true;
    }

    /**
     * Function use for add bind config file
     * @param $serviceName
     *
     * @return bool
     *@author: tat.pham
     *
     */
    private function updateBindFile($serviceName)
    {
        $strClassRepository = "\\App\\Services\\$serviceName" . 'Service\\' . $serviceName . "Service::class,\n";

        $bindFilePath = config_path('bind.php');
        $contentModel = file_get_contents($bindFilePath);
        //Separate auto code and manual code
        $arrBindContent = explode(self::SEPARATE_CODE, $contentModel);
        $contentModel = $arrBindContent[0] . $strClassRepository . '    ' . self::SEPARATE_CODE . $arrBindContent[1];
        file_put_contents($bindFilePath, $contentModel);
        return true;
    }
}
