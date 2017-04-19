<?php
// DIC configuration


$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c['settings']['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c['settings']['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// elastic
$container['elastic'] = function ($c) {
    $settings = $settings = $c['settings']['elastica'];
    return new Elastica\Client($settings);
};

$container['geo'] = function ($c) {
    return new Cobak78\GeoHash\GeoHash();
};

$container['geo_service'] = function ($c) {
    return new Cobak78\Services\GeoService($c['logger'], $c['elastic'], $c['geo']);
};

$container['actions.index'] = function ($c) {
    return new Cobak78\Controller\IndexAction($c['renderer'], $c['geo_service']);
};
