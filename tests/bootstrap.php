<?php

class App {
    /** @var Silly\Application */
    static $app;
}

App::$app = require __DIR__ . '/../app.php';

$autoLoader = require __DIR__ . '/../vendor/autoload.php';
$autoLoader->addPsr4('m8rge\\tests\\', __DIR__. '/../tests');
