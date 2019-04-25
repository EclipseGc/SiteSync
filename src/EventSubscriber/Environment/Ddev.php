<?php

namespace EclipseGc\SiteSync\EventSubscriber\Environment;

use EclipseGc\SiteSync\Action\RunProcess;
use EclipseGc\SiteSync\Environment\Ddev as DdevEnvironment;
use EclipseGc\SiteSync\Event\GetEnvironmentObjectEvent;
use EclipseGc\SiteSync\Event\GetEnvironmentsEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Ddev implements EventSubscriberInterface {

  use RunProcess;

  public static function getSubscribedEvents() {
    $events[SiteSyncEvents::GET_ENVIRONMENTS] = 'onGetEnvironments';
    $events[SiteSyncEvents::GET_ENVIRONMENT_OBJECT] = 'onGetEnvironmentObject';
    return $events;
  }

  public function onGetEnvironments(GetEnvironmentsEvent $event) {
    $process = $this->startProcess($event->getOutput(), "which ddev");
    if ($process->isSuccessful()) {
      $event->addAvailableEnvironment(DdevEnvironment::ID);
    }
  }

  public function onGetEnvironmentObject(GetEnvironmentObjectEvent $event) {
    $config = $event->getConfiguration();
    if ($config['environment'] === DdevEnvironment::ID) {
      $event->setEnvironmentObject(new DdevEnvironment($config, $event->getType()));
      $event->stopPropagation();
    }
  }

}
