<?php

namespace EclipseGc\SiteSync\EventSubscriber\Source;

use EclipseGc\SiteSync\Event\GetSourceObjectEvent;
use EclipseGc\SiteSync\Event\GetSourcesEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class SourceSubscriberBase implements EventSubscriberInterface {

  /**
   * @var \Psr\Container\ContainerInterface
   */
  protected $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  public static function getSubscribedEvents() {
    $events[SiteSyncEvents::GET_SOURCES] = 'onGetSources';
    $events[SiteSyncEvents::GET_SOURCE_OBJECT] = 'onGetSourceClass';
    return $events;
  }

  public function onGetSources(GetSourcesEvent $event) {
    $type = $event->getConfiguration()->get('type');
    if (in_array($type, $this->getCompatibility($type))) {
      $event->addSource($this->getType(), $this->getLabel());
    }
  }

  public function onGetSourceClass(GetSourceObjectEvent $event) {
    if ($event->getConfiguration()->get('source') === $this->getType()) {
      $event->setSourceObject($this->container->get($this->getServiceId()));
      $event->stopPropagation();
    }
  }

  abstract public function getType() : string ;

  abstract public function getLabel() : string ;

  abstract public function getServiceId() : string ;

  abstract public function getCompatibility(string $type = NULL) : array ;

}
