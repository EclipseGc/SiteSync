<?php

require __DIR__ . '/../vendor/autoload.php';

use EclipseGc\SiteSync\Command\Initialize;
use EclipseGc\SiteSync\Command\Pull;
use EclipseGc\SiteSync\Command\PullDb;
use Symfony\Component\Console\Application;

/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
$container = include __DIR__ .'/../includes/container.php';

$application = new Application('siteSync', '0.0.1');

$application->add(new Initialize(NULL, $container->get('event_dispatcher')));
$application->add(new Pull(NULL, $container->get('event_dispatcher')));
$application->add(new PullDb(NULL, $container->get('event_dispatcher')));

$application->run();
