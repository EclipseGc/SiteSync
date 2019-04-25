<?php


namespace EclipseGc\SiteSync\Event;


use EclipseGc\SiteSync\Environment\EnvironmentInterface;
use EclipseGc\SiteSync\Type\TypeInterface;
use Symfony\Component\EventDispatcher\Event;

class GetEnvironmentObjectEvent extends Event {

  /**
   * The siteSync configuration.
   *
   * @var array
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

  public function __construct(array $configuration, TypeInterface $type) {
    $this->configuration = $configuration;
    $this->type = $type;
  }

  public function getConfiguration() {
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