<?php

namespace EclipseGc\SiteSync\Event;

use EclipseGc\SiteSync\Configuration;
use EclipseGc\SiteSync\Source\SourceInterface;
use Symfony\Component\EventDispatcher\Event;

class GetSourceObjectEvent extends Event {

  /**
   * The siteSync configuration.
   *
   * @var \EclipseGc\SiteSync\Configuration
   */
  protected $configuration;

  /**
   * @var \EclipseGc\SiteSync\Source\SourceInterface
   */
  protected $typeObject;

  public function __construct(Configuration $configuration) {
    $this->configuration = $configuration;
  }

  /**
   * Get the siteSync configuration.
   *
   * @return \EclipseGc\SiteSync\Configuration
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  public function setSourceObject(SourceInterface $typeObject) {
    $this->typeObject = $typeObject;
  }

  public function getSourceObject() : SourceInterface {
    if (!$this->typeObject) {
      throw new \LogicException("You must set the type object before trying to get it.");
    }
    return $this->typeObject;
  }

}