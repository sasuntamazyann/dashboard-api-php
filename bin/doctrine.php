<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

require_once __DIR__ . '/../src/bootstrap.php';

$entityManager = $container->get(EntityManager::class);

ConsoleRunner::run(new SingleManagerProvider($entityManager));