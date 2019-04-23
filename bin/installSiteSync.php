<?php

require __DIR__ . '/../vendor/autoload.php';

use EclipseGc\SiteSync\Command\Install;
use Symfony\Component\Console\Application;

$application = new Application('installSiteSync', '0.0.1');
$command = new Install();

$application->add($command);
$application->setDefaultCommand($command->getName(), TRUE);

$application->run();
