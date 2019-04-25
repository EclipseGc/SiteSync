<?php

namespace EclipseGc\SiteSync\Type;

use EclipseGc\SiteSync\Event\GetEnvironmentObjectEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class TypeBase implements TypeInterface {

  /**
   * The siteSync configuration.
   *
   * @var array
   */
  protected $configuration;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  public function __construct(array $configuration, EventDispatcherInterface $dispatcher) {
    $this->configuration = $configuration;
    $this->dispatcher = $dispatcher;
  }

  protected function getEnvironmentObject() {
    $environmentObjectEvent = new GetEnvironmentObjectEvent($this->configuration, $this);
    $this->dispatcher->dispatch(SiteSyncEvents::GET_ENVIRONMENT_OBJECT, $environmentObjectEvent);
    return $environmentObjectEvent->getEnvironmentObject();
  }

}
