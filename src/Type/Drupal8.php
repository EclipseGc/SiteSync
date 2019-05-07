<?php

namespace EclipseGc\SiteSync\Type;

use EclipseGc\SiteSync\Dispatcher;

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

  public function getProjectType(): string {
    return self::ID;
  }


  public function getCompatibleSources() {
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