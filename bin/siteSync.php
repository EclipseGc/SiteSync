<?php

require __DIR__ . '/../vendor/autoload.php';

use EclipseGc\SiteSync\Command\Initialize;
use EclipseGc\SiteSync\Command\Pull;
use Symfony\Component\Console\Application;

$application = new Application('siteSync', '0.0.1');

$application->add(new Initialize());
$application->add(new Pull());

$application->run();
