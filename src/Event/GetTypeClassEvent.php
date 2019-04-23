<?php

namespace EclipseGc\SiteSync\Event;

use EclipseGc\SiteSync\Type\TypeInterface;
use Symfony\Component\EventDispatcher\Event;

class GetTypeClassEvent extends Event {

  /**
   * @var string
   */
  protected $type;

  /**
   * @var \EclipseGc\SiteSync\Type\TypeInterface
   */
  protected $typeObject;

  public function __construct(string $type) {
    $this->type = $type;
  }

  public function getType() {
    return $this->type;
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