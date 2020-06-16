<?php

/**
 * This file is part of generate-code
 *
 * (c) Tat Pham <tat.pham89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WindCloud\GenerateCode\GenerateModel;

use WindCloud\GenerateCode\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModelGenerator
{
    use Builder;

    const SEPARATE_CODE = '//*************************************************';
    const INT_TEMPLATE = 'intTemplate.txt';
    const DATETIME_TEMPLATE = 'dateTemplate.txt';
    const CREATED_UPDATED_AT_TEMPLATE = 'createdUpdatedAtTemplate.txt';
    const DEFAULT_TEMPLATE = 'defaultTemplate.txt';

    /**
     * Function use for generate model from array mapping table
     * @author: tat.pham
     *
     * @param array $arrTableModelMapping
     * @param array|null $arrMakeConstant
     * @return int
     * @throws \Exception
     */
    public function generateModel(array $arrTableModelMapping, array $arrMakeConstant = null)
    {
        try {
            $getTableList = Arr::pluck(Schema::getAllTables(), 'tablename');
            $count = 0;
            echo "======START GENERATE MODEL======\n";
            foreach ($getTableList as $key => $table) {
                if ($table === 'migrations') {
                    continue;
                }
                //Check array model mapping is exist
                if (!isset($arrTableModelMapping[$table])) {
                    continue;
                }
                $modelName = $arrTableModelMapping[$table];
                // Get constant key if exist
                $constantKey = Arr::get($arrMakeConstant, $table, null);
                echo 'Generate model:' . $modelName . 'Model.php' . "\n";
                $this->createModel($table, $modelName, $constantKey);
                $count++;
            }
            echo "======END GENERATE MODEL======\n";
            echo "\n";
            echo "\n";
            return $count;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param string $tableName
     * @param string $modelName
     * @param string|null $columnName
     * @return bool
     */
    private function createModel(string $tableName, string $modelName, string $columnName = null)
    {
        //$columnList = Schema::getColumnListing($tableName);
        $columnList = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails($tableName)->getColumns();
        //Model path
        $modelFolder = app_path(implode(DIRECTORY_SEPARATOR, ['Models']));
        //Create folder model if not exist
        $isExist = file_exists($modelFolder);
        if ($isExist === false) {
            mkdir($modelFolder, 0777, true);
        }
        //Model name path
        $modelPath = $modelFolder . DIRECTORY_SEPARATOR . $modelName . 'Model.php';
        if (file_exists($modelPath)) {
            $contentModel = file_get_contents($modelPath);
        } else {
            $templateModelPath = app_path(implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'GenerateModel', 'Templates', 'templateModel.txt']));
            //Get template model
            $templateContentNewModel = file_get_contents($templateModelPath);
            $contentModel = self::bind($templateContentNewModel, array(
                'className' => $modelName,
                'tableName' => $tableName,
                'primaryKey' => $this->getPrimaryKey($tableName)
            ));
        }
        // Separate auto code and manual code
        $arrModelContent = explode(self::SEPARATE_CODE, $contentModel);
        // Generate constant key
        $strConstants = "\n";
        if ($columnName !== null) {
            $strConstants = $this->generateConstantsKey($tableName, $columnName);
        }

        $strGetterSetterMethod = "\n" . $strConstants;
        foreach ($columnList as $key => $column) {
            $strGetterSetterMethod .= $this->generateGetterSetterMethod($column, $modelName);
        }
        //Merge manual code with auto code
        $contentModel = $arrModelContent[0] . self::SEPARATE_CODE . $strGetterSetterMethod . "}\n";
        file_put_contents($modelPath, $contentModel);
        return true;
    }

    /**
     * Convert snack string to camel string
     * @author: tat.pham
     *
     * @param $name
     *
     * @return string
     */
    private function convertSnackToCamel($name)
    {
        $nameAfterConvert = implode('', array_map('ucfirst', explode('_', $name)));
        return $nameAfterConvert;
    }

    /**
     * Function use for get primary key of a table
     * @author: tat.pham
     *
     * @param $tableName
     *
     * @return string
     */
    private function getPrimaryKey(string $tableName): string
    {
        return Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($tableName)['primary']->getColumns()[0];
    }

    /**
     * Function use for generate getter setter method
     * @param $column
     *
     * @param $modelName
     * @return bool|string
     * @author: tat.pham
     *
     */
    private function generateGetterSetterMethod($column, $modelName)
    {
        //Column name
        $columnName = $column->getName();
        //Get type from columnType
        $type = $column->getType()->getName();
        //declare variable content getter setter method
        switch ($type) {
            case 'int':
            case 'tinyint':
            case 'integer':
            case 'bigint':
            case 'smallint':
                $strGetterSetterMethod = $this->generateGetterSetterFromTemplate($columnName, $modelName, self::INT_TEMPLATE);
                break;
            case 'date':
            case 'timestamp':
            case 'datetime':
                $strGetterSetterMethod = $this->generateGetterSetterFromTemplate($columnName, $modelName, ($columnName === 'created_at' || $columnName === 'updated_at') ? self::CREATED_UPDATED_AT_TEMPLATE : self::DATETIME_TEMPLATE);
                break;
            default:
                $strGetterSetterMethod = $this->generateGetterSetterFromTemplate($columnName, $modelName, self::DEFAULT_TEMPLATE);
                break;
        }
        return $strGetterSetterMethod;
    }

    /**
     * Function use for generate setter getter method from template
     * @author: tat.pham
     *
     * @param $columnName
     * @param $template
     *
     * @return bool|string
     */
    private function generateGetterSetterFromTemplate($columnName, $modelName, $template)
    {
        //The path of template
        switch ($template) {
            case self::INT_TEMPLATE:
                $templateGetterSetterPath = app_path(implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'GenerateModel', 'Templates', self::INT_TEMPLATE]));
                break;
            case self::DATETIME_TEMPLATE:
                $templateGetterSetterPath = app_path(implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'GenerateModel', 'Templates', self::DATETIME_TEMPLATE]));
                break;
            case self::CREATED_UPDATED_AT_TEMPLATE:
                $templateGetterSetterPath = app_path(implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'GenerateModel', 'Templates', self::CREATED_UPDATED_AT_TEMPLATE]));
                break;
            default:
                $templateGetterSetterPath = app_path(implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'GenerateModel', 'Templates', self::DEFAULT_TEMPLATE]));
                break;
        }
        //Convert snack to calma
        $variable = self::snakeToCamel($columnName);
        $strColumnName = self::convertSnackToCamel($columnName);
        //Declare variable result
        $strResult = file_get_contents($templateGetterSetterPath);
        //binding data
        $strResult = self::bind($strResult, [
            'constantColumn' => strtoupper($columnName),
            'modelName' => $modelName,
            'columnName' => $columnName,
            'columnFunction' => $strColumnName,
            'variable' => $variable
        ]);

        return $strResult;
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @return string
     */
    private function generateConstantsKey(string $tableName, string $columnName)
    {
        // String constants
        $strConstants = "\n";
        // Get all data of table need make constant
        $keyList = Arr::pluck(DB::select('SELECT `' . $columnName . '` FROM ' . $tableName), $columnName);

        foreach ($keyList as $key => $value) {
            $strConstants .= '    const ' . strtoupper($value) . ' = \'' . $value . "';\n";
        }

        return $strConstants;
    }
}
