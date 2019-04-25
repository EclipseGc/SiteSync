<?php


namespace EclipseGc\SiteSync\Event;


use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

class GetEnvironmentsEvent extends Event {

  /**
   * The partial siteSync configuration.
   *
   * @var array
   */
  protected $configuration;

  /**
   * The console output.
   *
   * @var \Symfony\Component\Console\Output\OutputInterface
   */
  protected $output;

  /**
   * The available environment types.
   *
   * @var string[]
   */
  protected $environments;

  public function __construct(array $configuration, OutputInterface $output) {
    $this->configuration = $configuration;
    $this->output = $output;
  }

  /**
   * @return array
   */
  public function getConfiguration(): array {
    return $this->configuration;
  }

  /**
   * @return \Symfony\Component\Console\Output\OutputInterface
   */
  public function getOutput(): OutputInterface {
    return $this->output;
  }

  public function addAvailableEnvironment($environment) {
    $this->environments[] = $environment;
  }

  public function getAvailableEnvironments() : array {
    return $this->environments;
  }

}
