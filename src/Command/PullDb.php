<?php

namespace EclipseGc\SiteSync\Command;

use EclipseGc\SiteSync\Action\DumpRemoteDatabaseViaSsh;
use EclipseGc\SiteSync\Event\GetEnvironmentObjectEvent;
use EclipseGc\SiteSync\Event\GetTypeClassEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use EclipseGc\SiteSync\Type\TypeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class PullDb extends Command {

  use DumpRemoteDatabaseViaSsh;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $fs;

  /**
   * @var array
   */
  protected $configuration;

  public function __construct($name = NULL, EventDispatcherInterface $dispatcher) {
    $this->dispatcher = $dispatcher;
    parent::__construct($name);
  }

  protected function configure() {
    $this->setName('pull-db')
      ->setDescription('Pull the remote database into the local environment.');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->fs = new Filesystem();
    if (!$this->fs->exists('.siteSync.yml')) {
      $output->writeln("The site has not yet been initialized. Run the init command");
      return;
    }
    $this->configuration = Yaml::parseFile('.siteSync.yml');
    $type = $this->getTypeObject();
    $environment = $this->getEnvironmentObject($type);
    $type->getDump($output);
    $environment->importDb($input, $output);
  }

  protected function getTypeObject() {
    $typeObjectEvent = new GetTypeClassEvent($this->configuration);
    $this->dispatcher->dispatch($typeObjectEvent, SiteSyncEvents::GET_TYPE_CLASS);
    return $typeObjectEvent->getTypeObject();
  }

  protected function getEnvironmentObject(TypeInterface $type) {
    $environmentObjectEvent = new GetEnvironmentObjectEvent($this->configuration, $type);
    $this->dispatcher->dispatch($environmentObjectEvent, SiteSyncEvents::GET_ENVIRONMENT_OBJECT);
    return $environmentObjectEvent->getEnvironmentObject();
  }

}