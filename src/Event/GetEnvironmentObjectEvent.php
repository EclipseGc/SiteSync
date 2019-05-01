<?php


namespace EclipseGc\SiteSync\Event;


use EclipseGc\SiteSync\Configuration;
use EclipseGc\SiteSync\Environment\EnvironmentInterface;
use EclipseGc\SiteSync\Source\SourceInterface;
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
   * @var \EclipseGc\SiteSync\Source\SourceInterface
   */
  protected $type;

  public function __construct(Configuration $configuration, SourceInterface $type) {
    $this->configuration = $configuration;
    $this->type = $type;
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
