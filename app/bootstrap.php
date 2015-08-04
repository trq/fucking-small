<?php

require __DIR__ . '/../vendor/autoload.php';

use FuckingSmall\App;

$requestPath = __DIR__ . '/web/' . $_SERVER['REQUEST_URI'];

/**
 * Serve static files, or content via the application
 */
if (file_exists($requestPath) && is_file($requestPath)) {
    return false;
} else {
    $app = new App(__DIR__);
    $app->run();
}
