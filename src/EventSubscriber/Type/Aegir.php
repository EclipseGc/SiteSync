<?php

namespace EclipseGc\SiteSync\EventSubscriber\Type;

use EclipseGc\SiteSync\Event\GetTypeClassEvent;
use EclipseGc\SiteSync\Event\GetTypesEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use EclipseGc\SiteSync\Type\Aegir as AegirType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Aegir implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    $events[SiteSyncEvents::GET_TYPES] = 'onGetTypes';
    $events[SiteSyncEvents::GET_TYPE_CLASS] = 'onGetTypeClass';
    return $events;
  }

  public function onGetTypes(GetTypesEvent $event) {
    print_r($event);
    $event->addType("Aegir");
  }

  public function onGetTypeClass(GetTypeClassEvent $event) {
    if ($event->getType() === "Aegir") {
      $event->setTypeObject(new AegirType());
      $event->stopPropagation();
    }
  }

}
