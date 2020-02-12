<?php

namespace EclipseGc\SiteSync\Type;

use EclipseGc\SiteSync\Dispatcher;
use Symfony\Component\Console\Output\OutputInterface;

class Drupal8 implements TypeInterface {

  const ID = 'drupal8';

  const LABEL = 'Drupal 8';

  const SERVICE_ID = 'sitesync.type.drupal8';

  /**
   * The siteSync dispatcher.
   *
   * @var \EclipseGc\SiteSync\Dispatcher
   */
  protected $dispatcher;

  /**
   * @var \EclipseGc\SiteSync\Configuration
   */
  protected $configuration;

  public function __construct(Dispatcher $dispatcher) {
    $this->dispatcher = $dispatcher;
    $this->configuration = $dispatcher->getConfiguration();
  }

  public function getDumpCommands(OutputInterface $output) : array {
    $commands = [];
    $commands[] = ['mysql', '-e "SHOW TABLES LIKE \'cache\_%\';"'];
    $commands[] = ['mysqldump', '--ignore-table=db_name.table_name db_name > export.sql'];
    $commands[] = "mysqldump --no-data db_name table_name >> export.sql";
    return $commands;
  }

  public function getProjectType(): string {
    return self::ID;
  }

  public function getCompatibleSources() : array {
    $sources = $this->dispatcher->getSources();
    foreach ($sources as $key => $source) {
      $configuration = clone $this->configuration;
      $configuration->set('source', $source);
      $sourceObject = $this->dispatcher->getSourceObject($configuration);
      if (!in_array($sources, $sourceObject->getCompatibility())) {
        unset($sources[$key]);
      }
    }
    return $sources;
  }

}