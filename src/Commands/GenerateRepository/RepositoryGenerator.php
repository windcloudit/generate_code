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

class RepositoryGenerator
{
    use Builder;

    const SEPARATE_CODE = '//*************************************************';
    const SEPARATE_CODE_BIND_FILE = '//*******************AUTO GENERATED - DO NOT MODIFY FROM HERE******************************';
    const INTERFACE_TEMPLATE = 'interface.txt';
    const INTERFACE_IML_TEMPLATE = 'interfaceImpl.txt';

    /**
     * Function use for generate model from array mapping table
     * @author: tat.pham
     *
     * @param array $arrModel
     *
     * @return int
     */
    public function generateRepository(array $arrModel)
    {
        $count = 0;
        echo "======START GENERATE REPOSITORY======\n";
        foreach ($arrModel as $key => $model) {
            echo "Create repository: $model" . "Repository.php' \n";
            $this->createRepositoryInterface($model);
            echo "Create repository Impl: $model" . "RepositoryImpl.php\n";
            $this->createRepositoryIml($model);
            $count++;
        }
        //Update bind file
        $result = $this->updateBindFile($arrModel);
        echo "======END GENERATE REPOSITORY======\n";
        return $count;
    }

    /**
     * Function use for generate repository interface
     * @author: tat.pham
     *
     * @param $modelName
     *
     * @return bool
     */
    private function createRepositoryInterface($modelName)
    {
        //Model path
        $repositoryFolder = app_path(implode(DIRECTORY_SEPARATOR, ['Repositories', $modelName . 'Repository']));
        //Create folder model if not exist
        $isExist = file_exists($repositoryFolder);
        if ($isExist === false) {
            mkdir($repositoryFolder, 0777, true);
        }
        //Create repository Interface
        $repositoryInterfacePath = $repositoryFolder . DIRECTORY_SEPARATOR . $modelName . 'Repository.php';
        $templateRepositoryInterfacePath = app_path(implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'GenerateRepository', 'Templates', self::INTERFACE_TEMPLATE]));
        //Get template model
        $templateContentNewRepositoryInterface = file_get_contents($templateRepositoryInterfacePath);
        if (file_exists($repositoryInterfacePath)) {
            $contentModel = file_get_contents($repositoryInterfacePath);
        } else {
            $contentModel = self::bind($templateContentNewRepositoryInterface, array(
                'modelName' => $modelName,
                'modelNameParam' => lcfirst($modelName)
            ));
        }
        //Separate auto code and manual code
        $arrRepositoryContent = explode(self::SEPARATE_CODE, $contentModel);
        $arrRepositoryTemplateInterfaceContent = explode(self::SEPARATE_CODE, $templateContentNewRepositoryInterface);

        //Merge manual code with auto code
        $contentModel = $arrRepositoryContent[0] . self::SEPARATE_CODE . self::bind($arrRepositoryTemplateInterfaceContent[1], array(
                'modelName' => $modelName,
                'modelNameParam' => lcfirst($modelName)
            ));
        file_put_contents($repositoryInterfacePath, $contentModel);
        return true;
    }

    /**
     * Function use for generate repository impl
     * @author: tat.pham
     *
     * @param $modelName
     *
     * @return bool
     */
    private function createRepositoryIml($modelName)
    {
        //Model path
        $repositoryImlPath = app_path(implode(DIRECTORY_SEPARATOR, ['Repositories', $modelName . 'Repository', $modelName . 'RepositoryImpl.php']));
        //Create repository Interface
        $templateRepositoryInterfacePath = app_path(implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'GenerateRepository', 'Templates', self::INTERFACE_IML_TEMPLATE]));
        //Get template model
        $templateContentNewRepositoryImpl = file_get_contents($templateRepositoryInterfacePath);
        if (file_exists($repositoryImlPath)) {
            $contentModel = file_get_contents($repositoryImlPath);
        } else {
            $contentModel = $templateContentNewRepositoryImpl;
        }
        //Separate auto code and manual code
        $arrRepositoryContent = explode(self::SEPARATE_CODE, $contentModel);
        $arrRepositoryTemplateInterfaceContent = explode(self::SEPARATE_CODE, $templateContentNewRepositoryImpl);

        //Merge manual code with auto code
        $contentModel = $arrRepositoryContent[0] . self::SEPARATE_CODE . $arrRepositoryTemplateInterfaceContent[1];
        $contentModel = self::bind($contentModel, array(
            'modelName' => $modelName,
            'modelNameParam' => lcfirst($modelName)
        ));
        file_put_contents($repositoryImlPath, $contentModel);
        return true;
    }

    /**
     * Function use for add bind config file
     * @author: tat.pham
     *
     * @param $arrModel
     *
     * @return bool
     */
    private function updateBindFile($arrModel)
    {
        $strClassRepository = "\n";
        foreach ($arrModel as $key => $modelName) {
            $strClassRepository .= "    \\App\\Repositories\\$modelName" . 'Repository\\' . $modelName . "Repository::class,\n";
        }
        $bindFilePath = config_path('bind.php');
        $contentModel = file_get_contents($bindFilePath);
        //Separate auto code and manual code
        $arrBindContent = explode(self::SEPARATE_CODE_BIND_FILE, $contentModel);
        $contentModel = $arrBindContent[0] . self::SEPARATE_CODE_BIND_FILE . $strClassRepository . "\n];\n";
        file_put_contents($bindFilePath, $contentModel);
        return true;
    }
}
