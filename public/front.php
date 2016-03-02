<?php
date_default_timezone_set('UTC');

require __DIR__ . '/../vendor/autoload.php';

use Stardust\Core\Container;

$configurationDirectories = [
    __DIR__ . '/../app/configs',
];

$environment = getenv('APPLICATION_ENVIRONMENT') ?: 'dev';

$parameters = [
    'app.root'                      => __DIR__ . '/../',
    'app.environment'               => $environment,
    'app.configuration_directories' => $configurationDirectories,
    'app.charset'                   => 'UTF-8',
    'twig.debug'                    => false,
    'twig.auto_reload'              => true,
];

$container = Container::build($parameters);

$request = $container->get('request');

$application = $container->get('cached');

$response = $application->handle($request);

$response->send();
