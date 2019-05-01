<?php

namespace EclipseGc\SiteSync\Event;

use Symfony\Component\EventDispatcher\Event;

class GetSourcesEvent extends Event {

  /**
   * The array of available sources.
   *
   * @var string[]
   */
  protected $sources = [];

  public function addSource(string $sourceName) {
    $this->sources[] = $sourceName;
  }

  public function getSources() {
    return $this->sources;
  }

}
