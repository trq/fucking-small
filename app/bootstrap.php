<?php

require __DIR__ . '/../vendor/autoload.php';

use FrameworkModule\App;

$requestPath = __DIR__ . '/web/' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);;

/**
 * Serve static files, or content via the application
 */
if (file_exists($requestPath) && is_file($requestPath)) {
    return false;
} else {
    $app = new App(__DIR__);

    $app->bootstrap([
        new \FrameworkModule\FrameworkModule(),
        new \DemoModule\DemoModule()
    ]);

    $app->run();
}
