<?php

use m8rge\CountCommand;

$autoLoader = require 'vendor/autoload.php';
$autoLoader->addPsr4('m8rge\\', __DIR__. '/src');

$app = new Silly\Application('Word count', '@version@');
$container = \DI\ContainerBuilder::buildDevContainer();
$app->useContainer($container, true, true);

$container->set('app', $app);

$app->command('count filename', CountCommand::class)
    ->descriptions('Count unique words in file', [
        'filename' => 'Text file name',
    ]);

return $app;