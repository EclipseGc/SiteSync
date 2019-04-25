<?php

namespace EclipseGc\SiteSync\EventSubscriber\Type;

use EclipseGc\SiteSync\Event\GetTypeClassEvent;
use EclipseGc\SiteSync\Event\GetTypesEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use EclipseGc\SiteSync\Type\Drupal;
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
    $events[SiteSyncEvents::GET_TYPES] = 'onGetTypes';
    $events[SiteSyncEvents::GET_TYPE_CLASS] = 'onGetTypeClass';
    return $events;
  }

  public function onGetTypes(GetTypesEvent $event) {
    $event->addType($this::LABEL);
  }

  public function onGetTypeClass(GetTypeClassEvent $event) {
    if ($event->getConfiguration()['type'] === $this::LABEL) {
      $event->setTypeObject(new Drupal($event->getConfiguration(), $this->dispatcher));
      $event->stopPropagation();
    }
  }

}
