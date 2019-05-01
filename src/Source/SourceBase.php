<?php

namespace EclipseGc\SiteSync\Source;

use EclipseGc\SiteSync\Dispatcher;

abstract class SourceBase implements SourceInterface {

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

}
