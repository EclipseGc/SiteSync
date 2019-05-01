<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
$container = include __DIR__ .'/../includes/container.php';

$application = new Application('siteSync', '0.0.1');

$application->add($container->get('sitesync.command.init'));
$application->add($container->get('sitesync.command.pull'));
$application->add($container->get('sitesync.command.pulldb'));

$application->run();
