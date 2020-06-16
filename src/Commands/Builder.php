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

trait Builder
{
    /**
     * Bind variable to template.
     * @author: tat.pham
     *
     * @param string $template
     * @param array  $variables
     * @param string $start
     * @param string $end
     *
     * @return string
     */
    public static function bind($template, $variables = [], $start = '{', $end = '}')
    {
        $content = $template;
        foreach ($variables as $variableName => $variableValue) {
            $content = \str_replace($start . $variableName . $end, $variableValue, $content);
        }

        return $content;
    }

    /**
     * @param $str
     * @return string
     */
    public static function snakeToCamel($str)
    {
        // Remove underscores, capitalize words, squash, lowercase first.
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
    }

    /**
     * @param string $fullPath
     * @return bool
     */
    public static function fileExit(string $fullPath)
    {
        return file_exists($fullPath);
    }

    /**
     * @param string $name
     * @return string
     */
    public static function convertUpperCaseToCamelCase(string $name)
    {
        return strtolower(substr($name, 0, 1)) . substr($name, 1, strlen($name));
    }

    /**
     * @param string $name
     * @return string
     */
    public static function convertCamelCaseToUpperCase(string $name)
    {
        return strtoupper(substr($name, 0, 1)) . substr($name, 1, strlen($name));
    }

    /**
     * @param $path
     * @param bool $all
     * @return array
     */
    public static function getListDir($path, $all = false)
    {
        $arrFile = scandir($path);
        $arrDir = [];
        foreach ($arrFile as $file) {
            if ($all === false) {
                if (strpos($file, '.php') !== false) {
                    continue;
                }
            }
            if (strpos($file, '.') !== false && strpos($file, '.php') === false) {
                continue;
            }
            if (strpos($file, 'Auth') !== false) {
                continue;
            }
            $arrDir[] = $file;
        }

        return $arrDir;
    }
}
