<?php


namespace EclipseGc\SiteSync\EventSubscriber\Type;


use EclipseGc\SiteSync\Event\GetTypeObjectEvent;
use EclipseGc\SiteSync\Event\GetTypesEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class TypeSubscriberBase implements EventSubscriberInterface {

  /**
   * @var \Psr\Container\ContainerInterface
   */
  protected $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  public static function getSubscribedEvents() {
    $events[SiteSyncEvents::GET_TYPES] = 'onGetTypes';
    $events[SiteSyncEvents::GET_TYPE_OBJECT] = 'onGetTypeObject';
    return $events;
  }

  public function onGetTypes(GetTypesEvent $event) {
    $event->addType($this->getType(), $this->getLabel());
  }

  public function onGetTypeObject(GetTypeObjectEvent $event) {
    if ($event->getConfiguration()->get('type') === $this->getType()) {
      $event->setType($this->container->get($this->getServiceId()));
      $event->stopPropagation();
    }
  }

  abstract public function getType();

  abstract public function getLabel();

  abstract public function getServiceId();

}
