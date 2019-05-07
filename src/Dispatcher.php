<?php

namespace EclipseGc\SiteSync;

use EclipseGc\SiteSync\Event\GetEnvironmentObjectEvent;
use EclipseGc\SiteSync\Event\GetEnvironmentsEvent;
use EclipseGc\SiteSync\Event\GetSourceObjectEvent;
use EclipseGc\SiteSync\Event\GetSourcesEvent;
use EclipseGc\SiteSync\Event\GetTypeObjectEvent;
use EclipseGc\SiteSync\Event\GetTypesEvent;
use EclipseGc\SiteSync\Source\SourceInterface;
use EclipseGc\SiteSync\Type\TypeInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Dispatcher {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * The siteSync configuration.
   *
   * @var \EclipseGc\SiteSync\Configuration
   */
  protected $configuration;

  public function __construct(EventDispatcherInterface $dispatcher, Configuration $configuration) {
    $this->dispatcher = $dispatcher;
    $this->configuration = $configuration;
  }

  public function getConfiguration() {
    return $this->configuration;
  }

  public function getTypes() {
    $typesEvent = new GetTypesEvent();
    $this->dispatcher->dispatch($typesEvent, SiteSyncEvents::GET_TYPES);
    return $typesEvent->getTypes();
  }

  public function getTypeObject(Configuration $configuration = NULL) {
    if (!$configuration) {
      $configuration = $this->configuration;
    }
    $typeObjectEvent = new GetTypeObjectEvent($configuration);
    $this->dispatcher->dispatch($typeObjectEvent, SiteSyncEvents::GET_TYPE_OBJECT);
    return $typeObjectEvent->getType();
  }

  public function getSources(Configuration $configuration = NULL) {
    if (!$configuration) {
      $configuration = $this->configuration;
    }
    $sourcesEvent = new GetSourcesEvent($configuration);
    $this->dispatcher->dispatch($sourcesEvent, SiteSyncEvents::GET_SOURCES);
    return $sourcesEvent->getSources();
  }

  public function getSourceObject(Configuration $configuration = NULL) {
    if (!$configuration) {
      $configuration = $this->configuration;
    }
    $sourceObjectEvent = new GetSourceObjectEvent($configuration);
    $this->dispatcher->dispatch($sourceObjectEvent, SiteSyncEvents::GET_SOURCE_OBJECT);
    return $sourceObjectEvent->getSourceObject();
  }

  public function getEnvironments(OutputInterface $output, Configuration $configuration = NULL) {
    if (!$configuration) {
      $configuration = $this->configuration;
    }
    $environmentTypes = new GetEnvironmentsEvent($configuration, $output);
    $this->dispatcher->dispatch($environmentTypes, SiteSyncEvents::GET_ENVIRONMENTS);
    return $environmentTypes->getAvailableEnvironments();
  }

  public function getEnvironmentObject(TypeInterface $type = NULL, SourceInterface $source = NULL, Configuration $configuration = NULL) {
    if (!$configuration) {
      $configuration = $this->configuration;
    }
    if (!$type) {
      $type = $this->getTypeObject($configuration);
    }
    if (!$source) {
      $source = $this->getSourceObject($configuration);
    }
    $environmentObjectEvent = new GetEnvironmentObjectEvent($configuration, $type, $source);
    $this->dispatcher->dispatch($environmentObjectEvent, SiteSyncEvents::GET_ENVIRONMENT_OBJECT);
    return $environmentObjectEvent->getEnvironmentObject();
  }

}