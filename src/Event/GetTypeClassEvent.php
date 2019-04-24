<?php

namespace EclipseGc\SiteSync\Event;

use EclipseGc\SiteSync\Type\TypeInterface;
use Symfony\Component\EventDispatcher\Event;

class GetTypeClassEvent extends Event {

  /**
   * The siteSync configuration.
   *
   * @var array
   */
  protected $configuration;

  /**
   * @var \EclipseGc\SiteSync\Type\TypeInterface
   */
  protected $typeObject;

  public function __construct(array $configuration) {
    $this->configuration = $configuration;
  }

  public function getConfiguration() {
    return $this->configuration;
  }

  public function setTypeObject(TypeInterface $typeObject) {
    $this->typeObject = $typeObject;
  }

  public function getTypeObject() : TypeInterface {
    if (!$this->typeObject) {
      throw new \LogicException("You must set the type object before trying to get it.");
    }
    return $this->typeObject;
  }

}