<?php
// DIC configuration


$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// elastic
$container['elastic'] = function ($c) {
    $settings = $settings = $c->get('settings')['elastica'];
    return new Elastica\Client($settings);
};

$container['geo'] = function ($c) {
    return new Cobak78\GeoHash\GeoHash();
};
