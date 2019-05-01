<?php

namespace EclipseGc\SiteSync\EventSubscriber\Source;

use EclipseGc\SiteSync\Dispatcher;
use EclipseGc\SiteSync\Event\GetSourceObjectEvent;
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
   * @param \EclipseGc\SiteSync\Dispatcher $dispatcher
   *   The siteSync dispatcher.
   */
  public function __construct(Dispatcher $dispatcher) {
    $this->dispatcher = $dispatcher;
  }

  public static function getSubscribedEvents() {
    $events[SiteSyncEvents::GET_SOURCES] = 'onGetSources';
    $events[SiteSyncEvents::GET_SOURCE_CLASS] = 'onGetSourceClass';
    return $events;
  }

  public function onGetSources(GetSourcesEvent $event) {
    $event->addSource("Aegir");
  }

  public function onGetSourceClass(GetSourceObjectEvent $event) {
    if ($event->getConfiguration()->get('type') === "Aegir") {
      $event->setSourceObject(new AegirType($this->dispatcher));
      $event->stopPropagation();
    }
  }

}
