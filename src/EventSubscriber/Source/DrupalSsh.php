<?php

namespace EclipseGc\SiteSync\EventSubscriber\Source;

use EclipseGc\SiteSync\Event\GetSourceClassEvent;
use EclipseGc\SiteSync\Event\GetSourcesEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use EclipseGc\SiteSync\Source\Drupal;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DrupalSsh implements EventSubscriberInterface {

  const LABEL = "Drupal across ssh";

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
    $events[SiteSyncEvents::GET_SOURCE_CLASS] = 'onGetSourcesClass';
    return $events;
  }

  public function onGetSources(GetSourcesEvent $event) {
    $event->addType($this::LABEL);
  }

  public function onGetSourcesClass(GetSourceClassEvent $event) {
    if ($event->getConfiguration()['type'] === $this::LABEL) {
      $event->setSourceObject(new Drupal($event->getConfiguration(), $this->dispatcher));
      $event->stopPropagation();
    }
  }

}
