<?php

namespace EclipseGc\SiteSync\Event;

use EclipseGc\SiteSync\Configuration;
use Symfony\Component\EventDispatcher\Event;

class GetSourcesEvent extends Event {

  /**
   * The array of available sources.
   *
   * @var string[]
   */
  protected $sources = [];

  /**
   * @var \EclipseGc\SiteSync\Configuration
   */
  protected $configuration;

  public function __construct(Configuration $configuration) {
    $this->configuration = $configuration;
  }

  public function getConfiguration() {
    return $this->configuration;
  }

  public function addSource(string $id, string $label) {
    $this->sources[$id] = $label;
  }

  public function getSources() {
    return $this->sources;
  }

}
