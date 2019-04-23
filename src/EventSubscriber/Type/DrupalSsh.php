<?php

namespace EclipseGc\SiteSync\EventSubscriber\Type;

use EclipseGc\SiteSync\Event\GetTypeClassEvent;
use EclipseGc\SiteSync\Event\GetTypesEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use EclipseGc\SiteSync\Type\Drupal;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DrupalSsh implements EventSubscriberInterface {

  const LABEL = "Drupal across ssh";

  public static function getSubscribedEvents() {
    $events[SiteSyncEvents::GET_TYPES] = 'onGetTypes';
    $events[SiteSyncEvents::GET_TYPE_CLASS] = 'onGetTypeClass';
    return $events;
  }

  public function onGetTypes(GetTypesEvent $event) {
    print_r($event);
    $event->addType($this::LABEL);
  }

  public function onGetTypeClass(GetTypeClassEvent $event) {
    if ($event->getType() === $this::LABEL) {
      $event->setTypeObject(new Drupal());
      $event->stopPropagation();
    }
  }

}
