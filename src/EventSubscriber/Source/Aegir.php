<?php

namespace EclipseGc\SiteSync\EventSubscriber\Source;

use EclipseGc\SiteSync\Event\GetSourceClassEvent;
use EclipseGc\SiteSync\Event\GetSourcesEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use EclipseGc\SiteSync\Source\Aegir as AegirType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Aegir implements EventSubscriberInterface {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * Aegir constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   */
  public function __construct(EventDispatcherInterface $dispatcher) {
    $this->dispatcher = $dispatcher;
  }

  public static function getSubscribedEvents() {
    $events[SiteSyncEvents::GET_SOURCES] = 'onGetSources';
    $events[SiteSyncEvents::GET_SOURCE_CLASS] = 'onGetSourceClass';
    return $events;
  }

  public function onGetSources(GetSourcesEvent $event) {
    $event->addType("Aegir");
  }

  public function onGetSourceClass(GetSourceClassEvent $event) {
    if ($event->getConfiguration()['type'] === "Aegir") {
      $event->setSourceObject(new AegirType($event->getConfiguration(), $this->dispatcher));
      $event->stopPropagation();
    }
  }

}
