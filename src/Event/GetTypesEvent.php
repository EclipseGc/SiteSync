<?php

namespace EclipseGc\SiteSync\Event;

use Symfony\Component\EventDispatcher\Event;

class GetTypesEvent extends Event {

  /**
   * The array of available types.
   *
   * @var string[]
   */
  protected $types = [];

  public function addType(string $typeName) {
    $this->types[] = $typeName;
  }

  public function getTypes() {
    return $this->types;
  }

}
