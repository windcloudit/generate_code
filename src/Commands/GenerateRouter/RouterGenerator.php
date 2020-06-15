<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019 Musashino Project. All rights reserved.
 * -------------------------------------------------------------------------------------------------------------
 * NOTICE:  All information contained herein is, and remains
 * the property of Persol Process & Technology Vietnam and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Persol Process & Technology Vietnam
 * and its suppliers and may be covered by Vietnamese Law,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Persol Process & Technology Vietnam.
 */

namespace WindCloud\GenerateCode\GenerateRouter;

use WindCloud\GenerateCode\Builder;

/**
 *
 * @category   WindCloud\GenerateCode\GenerateRouter
 *
 * @author     Tat.Pham <tat.pham@inte.co.jp>
 * @copyright  2017 PERSOL PROCESS & TECHNOLOGY VIETNAM CO., LTD.
 *
 * @version    1.0
 *
 * @see       https://ppt-gbc.backlog.com/git/DEV_MUSASINO/musashino_BE.git
 * @since     File available since Release 1.0
 */
class RouterGenerator
{
    use Builder;

    public function generateRouter(string $prefix, string $method, string $controller, string $action, string $url = '/', string $routeType = 'web'): ?bool
    {
        try {
            echo "======START GENERATE ROUTER======\n";
            echo "Route: Route::$method('$url', '$controller@$action')->name('$routeType.$prefix.$action');\"\n";
            $this->createRoute($prefix, $method, $controller, $action, $url, $routeType);
            echo "======END GENERATE ROUTER======\n";
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param string $prefix
     * @param string $method
     * @param string $controller
     * @param string $action
     * @param string $url
     * @param string $routeType
     * @return bool
     */
    private function createRoute(string $prefix, string $method, string $controller, string $action, string $url, string $routeType)
    {
        // Get router file
        if ($routeType === 'web') {
            $routeFile = base_path('routes/web.php');
        } else {
            $routeFile = base_path('routes/api.php');
        }

        $contentRoute = file_get_contents($routeFile);
        // Check exist prefix or not
        $prefixName = "Route::prefix('$prefix')->group(function () {";
        $pos = strpos($contentRoute, $prefixName);

        $newRoute = "    Route::$method('$url', '$controller@$action')->name('$routeType.$prefix.$action');";
        if ($pos === false) {
            // Create new route with prefix
            $contentRouteNew = $contentRoute . "\n" . $prefixName . "\n" . $newRoute . "\n" . '});';
        } else {
            // Update route with that prefix
            $arrContent = explode($prefixName, $contentRoute);
            $contentRouteNew = $arrContent[0] . $prefixName . "\n" . $newRoute . $arrContent[1];
        }
        file_put_contents($routeFile, $contentRouteNew);
        return true;
    }
}
