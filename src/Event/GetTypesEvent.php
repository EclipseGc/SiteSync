<?php

namespace EclipseGc\SiteSync\Event;

use Symfony\Component\EventDispatcher\Event;

class GetTypesEvent extends Event {

  protected $types = [];

  public function addType(string $type, string $label) {
    $this->types[$type] = $label;
  }

  public function getTypes() {
    return $this->types;
  }

}
