<?php


namespace EclipseGc\SiteSync\Event;


use EclipseGc\SiteSync\Configuration;
use EclipseGc\SiteSync\Environment\EnvironmentInterface;
use EclipseGc\SiteSync\Source\SourceInterface;
use EclipseGc\SiteSync\Type\TypeInterface;
use Symfony\Component\EventDispatcher\Event;

class GetEnvironmentObjectEvent extends Event {

  /**
   * The siteSync configuration.
   *
   * @var \EclipseGc\SiteSync\Configuration
   */
  protected $configuration;

  /**
   * The environment object.
   *
   * @var \EclipseGc\SiteSync\Environment\EnvironmentInterface
   */
  protected $environment;

  /**
   * The project type.
   *
   * @var \EclipseGc\SiteSync\Type\TypeInterface
   */
  protected $type;

  /**
   * The source type.
   *
   * @var \EclipseGc\SiteSync\Source\SourceInterface
   */
  protected $source;

  public function __construct(Configuration $configuration, TypeInterface $type, SourceInterface $source) {
    $this->configuration = $configuration;
    $this->type = $type;
    $this->source = $source;
  }

  /**
   * @return \EclipseGc\SiteSync\Configuration
   */
  public function getConfiguration() : Configuration {
    return $this->configuration;
  }

  public function getType() {
    return $this->type;
  }

  public function getSource() {
    return $this->source;
  }

  public function setEnvironmentObject(EnvironmentInterface $environment) {
    $this->environment = $environment;
  }

  public function getEnvironmentObject() : EnvironmentInterface {
    if (!$this->environment) {
      throw new \LogicException("You must set the environment object before trying to retrieve it.");
    }
    return $this->environment;
  }

}
