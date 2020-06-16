<?php

declare(strict_types=1);

namespace WindCloud\GenerateCode;

/**
 * Class ControllerGenerator
 * @package WindCloud\GenerateCode\GenerateController
 */
class ControllerGenerator
{
    use Builder;

    const CONTROLLER_TEMPLATE = 'controller.txt';
    const ACTION_TEMPLATE = 'action.txt';

    /**
     * Function use for generate controller
     * @param string $controllerName
     * @param string $controllerFolder
     * @param string $serviceName
     * @param bool $isPublishController
     * @return boolean
     * @throws \Exception
     * @author: tat.pham
     */
    public function generateController(string $controllerFolder, string $controllerName, $serviceName, $isPublishController): ?bool
    {
        try {
            echo "======START GENERATE CONTROLLER======\n";
            echo "Create controller: $controllerFolder/$controllerName" . "Controller.php' \n";
            $this->createController($controllerFolder, $controllerName, $serviceName, $isPublishController);
            echo "======END GENERATE CONTROLLER======\n";
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param string $controllerFolder
     * @param string $controllerFile
     * @param string $method
     * @param string $actionName
     * @param string $prefix
     * @param string $url
     * @param string $actionType
     * @return bool
     * @throws \Exception
     */
    public function generateAction(string $controllerFolder, string $controllerFile, string $method, string $actionName, string $prefix, string $url, $actionType = 'web')
    {
        try {
            echo "======START GENERATE ACTION======\n";
            echo "Create action: $actionName()" . "in $controllerFolder/$controllerFile" . "\n";
            $this->createAction($controllerFolder, $controllerFile, $method, $actionName, $prefix, $url, $actionType);
            echo "======END GENERATE ACTION======\n";
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param string $controllerFolder
     * @param string $controllerName
     * @param string $serviceName
     * @param bool $isPublishController
     * @return bool
     * @throws \Exception
     */
    private function createController(string $controllerFolder, string $controllerName, $serviceName, $isPublishController)
    {
        $folder = "Http/Controllers/$controllerFolder";
        $controllerFolderPath = app_path($folder);
        //Create folder service if not exist
        $isExist = self::fileExit($controllerFolderPath);
        if ($isExist === false) {
            mkdir($controllerFolderPath, 0755, true);
        }
        // Check controller exist or not
        $fullControllerPath = "$controllerFolderPath/$controllerName" . 'Controller.php';
        if (self::fileExit(app_path($fullControllerPath))) {
            throw new \Exception(__($controllerName . 'Controller.php already existed, please check again'));
        }

        //Create repository Interface
        $templateControllerPath = app_path(implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'GenerateController', 'Templates', self::CONTROLLER_TEMPLATE]));

        //Get template model
        $templateContentNewController = file_get_contents($templateControllerPath);
        $contentController = self::bind($templateContentNewController, array(
            'controllerFolder' => $controllerFolder,
            'serviceName' => $serviceName,
            'controllerName' => $controllerName,
            'serviceNameCamel' => self::convertUpperCaseToCamelCase($controllerName),
            'middlewareGuest' => $isPublishController ? '$this->middleware(\'guest\');' : ''
        ));

        file_put_contents($fullControllerPath, $contentController);
        return true;
    }

    /**
     * @param string $controllerFolder
     * @param string $controllerFile
     * @param string $method
     * @param string $actionName
     * @param string $prefix
     * @param string $url
     * @param string $actionType
     * @throws \Exception
     */
    private function createAction(string $controllerFolder, string $controllerFile, string $method, string $actionName, string $prefix, string $url, string $actionType = 'web')
    {
        $folder = "Http/Controllers/$controllerFolder/$controllerFile";
        $controllerFolderPath = app_path($folder);

        $templateActionPath = app_path(implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'GenerateController', 'Templates', self::ACTION_TEMPLATE]));
        $templateContentNewAction = file_get_contents($templateActionPath);
        $contentAction = self::bind($templateContentNewAction, array(
            'actionName' => $actionName,
        ));
        $contentController = file_get_contents($controllerFolderPath);

        $lastBracket = strrpos($contentController, '}');
        // get first content of test case file from 0 to last bracket
        $firstContentController = substr($contentController, 0, $lastBracket);
        $newContentTestCase = $firstContentController . $contentAction . "\n" . '}';

        file_put_contents($controllerFolderPath, $newContentTestCase);

        // Generate router
        $generateRouter = new RouterGenerator();
        $pos = strpos($controllerFile, '.');
        $controllerName = $controllerFolder . '\\' . substr($controllerFile, 0, $pos);
        $generateRouter->generateRouter($prefix, $method, $controllerName, $actionName, $url, $actionType);

        // Generate test case
        $testFileName = substr($controllerFile, 0, strpos($controllerFile, 'Controller'));
        $generateTestCase = new TestCaseGenerator();
        $generateTestCase->generateTestCase(
            $controllerFolder,
            $testFileName,
            $actionName,
            $method,
            $generateTestCase::TEST_CONTROLLER,
            $actionType . '.' . $prefix . '.' . $actionName
        );
    }
}
