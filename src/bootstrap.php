<?php

require_once __DIR__ . "/../vendor/autoload.php";

date_default_timezone_set('UTC');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_ENV['APP_DEBUG'] === "true") {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/dependencies.php');
$container = $builder->build();