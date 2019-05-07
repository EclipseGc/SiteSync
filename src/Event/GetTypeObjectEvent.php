<?php

namespace EclipseGc\SiteSync\Event;

use EclipseGc\SiteSync\Configuration;
use EclipseGc\SiteSync\Type\TypeInterface;
use Symfony\Component\EventDispatcher\Event;

class GetTypeObjectEvent extends Event {

  /**
   * @var \EclipseGc\SiteSync\Configuration
   */
  protected $configuration;

  /**
   * @var \EclipseGc\SiteSync\Type\TypeInterface
   */
  protected $type;

  public function __construct(Configuration $configuration) {
    $this->configuration = $configuration;
  }

  public function getConfiguration() {
    return $this->configuration;
  }

  public function setType(TypeInterface $type) {
    $this->type = $type;
  }

  public function getType() : TypeInterface {
    return $this->type;
  }

}
