<?php


namespace EclipseGc\SiteSync\Environment;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface EnvironmentInterface {

  public function init(InputInterface $input, OutputInterface $output);

  public function start(InputInterface $input, OutputInterface $output);

  public function importDb(InputInterface $input, OutputInterface $output);

}
