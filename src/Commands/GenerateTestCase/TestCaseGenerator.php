<?php

/**
 * This file is part of generate-code
 *
 * (c) Tat Pham <tat.pham89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WindCloud\GenerateCode\Commands\GenerateTestCase;

use WindCloud\GenerateCode\BaseGenerator;

class TestCaseGenerator extends BaseGenerator
{
    const TEST_CASE_CONSTROLLER_TEMPLATE_GET = 'testCaseControllerTemplateGet.txt';
    const TEST_CASE_CONSTROLLER_TEMPLATE_POST = 'testCaseControllerTemplatePost.txt';
    const TEST_CASE_CONSTROLLER_TEMPLATE_PUT = 'testCaseControllerTemplatePut.txt';
    const TEST_CASE_CONSTROLLER_TEMPLATE_DELETE = 'testCaseControllerTemplateDelete.txt';
    const TEST_CASE_REPOSITORY_TEMPLATE = 'testCaseRepositoryTemplate.txt';
    const TEST_CASE_SERVICE_TEMPLATE = 'testCaseServiceTemplate.txt';
    const TEST_CASE_DEFAULT_TEMPLATE = 'defaultemplate.txt';

    /**
     * @param string $folder
     * @param string $testFileName
     * @param string $action
     * @param string|null $method
     * @param string $testType
     * @param string $routeName
     * @return bool|null
     * @throws \Exception
     */
    public function generateTestCase(string $folder, string $testFileName, string $action, string $method = null, string $testType = self::TEST_CONTROLLER, string $routeName = '/'): ?bool
    {
        try {
            echo "======START GENERATE TEST CASE======\n";
            echo 'TestCase: ' . 'test' . self::convertCamelCaseToUpperCase($action) . ";\n";
            $this->createTestCase($folder, $testFileName, $action, $method, $testType, $routeName);
            echo "======END GENERATE TEST CASE======\n";
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param string $folder
     * @param string $testFileName
     * @param string $action
     * @param string $method
     * @param string $testType
     * @param string $routeName
     * @return bool
     */
    private function createTestCase(string $folder, string $testFileName, string $action, string $method, string $testType, string $routeName): bool
    {
        // Get test case file

        switch ($testType) {
            case self::TEST_REPOSITORY:
                $testFolder = 'Repositories';
                $template = self::TEST_CASE_REPOSITORY_TEMPLATE;
                break;
            case self::TEST_SERVICE:
                $testFolder = 'Services';
                $template = self::TEST_CASE_SERVICE_TEMPLATE;
                break;
            default:
                $testFolder = 'Http';
                switch ($method) {
                    case self::GET_METHOD:
                        $template = self::TEST_CASE_CONSTROLLER_TEMPLATE_GET;
                        break;
                    case self::POST_METHOD:
                        $template = self::TEST_CASE_CONSTROLLER_TEMPLATE_POST;
                        break;
                    case self::PUT_METHOD:
                        $template = self::TEST_CASE_CONSTROLLER_TEMPLATE_PUT;
                        break;
                    default:
                        $template = self::TEST_CASE_CONSTROLLER_TEMPLATE_DELETE;
                        break;
                }
                break;
        }

        //Create folder model if not exist
        $folderTestCasePath = base_path("tests/$testFolder/$folder");
        $isExist = file_exists($folderTestCasePath);
        if ($isExist === false) {
            mkdir($folderTestCasePath, 0777, true);
        }

        $testCaseFile = base_path("tests/$testFolder/$folder/$testFileName". 'Test.php');
        $isFileExist = file_exists($testCaseFile);
        if ($isFileExist === false) {
            $contentDefaultTemplate = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . (implode(DIRECTORY_SEPARATOR, ['Templates', self::TEST_CASE_DEFAULT_TEMPLATE])));
            $contentDefaultTestCaseTemplate = self::bind($contentDefaultTemplate, array(
                'testFolder' => $testFolder,
                'fileName' => self::convertCamelCaseToUpperCase($testFileName)
            ));
            file_put_contents($testCaseFile, $contentDefaultTestCaseTemplate);
        }

        $contentTestCase = file_get_contents($testCaseFile);

        // testCase name
        $testCaseName = 'test' . self::convertCamelCaseToUpperCase($action);

        $pos = strpos($contentTestCase, $testCaseName);
        // check file name and action name exist or not
        if ($pos !== false) {
            throw new \RuntimeException(__('This test case :testCaseName already exist in :testCaseFile', ['testCaseName' => $testCaseName, 'testCaseFile' => $testCaseFile]));
        }

        // get content template
        $templateContent = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . (implode(DIRECTORY_SEPARATOR, ['Templates', $template])));

        $contentTestCaseTemplate = self::bind($templateContent, array(
            'action' => self::convertCamelCaseToUpperCase($action),
            'routName' => $routeName
        ));

        $lastBracket = strrpos($contentTestCase, '}');
        // get first content of test case file from 0 to last bracket
        $firstContentTestCase = substr($contentTestCase, 0, $lastBracket);
        $newContentTestCase = $firstContentTestCase . $contentTestCaseTemplate . "\n" . '}';

        file_put_contents($testCaseFile, $newContentTestCase);
        return true;
    }
}
