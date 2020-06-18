<?php

/**
 * This file is part of generate-code
 *
 * (c) Tat Pham <tat.pham89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WindCloud\GenerateCode\Commands\GenerateRouter;

use WindCloud\GenerateCode\Commands\Builder;

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
