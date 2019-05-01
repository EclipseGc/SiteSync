<?php

namespace EclipseGc\SiteSync\Command;

use EclipseGc\SiteSync\Event\GetSourceClassEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Pull extends Command {

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * The filesystem object.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $fs;

  /**
   * The input.
   *
   * @var \Symfony\Component\Console\Input\InputInterface
   */
  protected $input;

  /**
   * The output.
   *
   * @var \Symfony\Component\Console\Output\OutputInterface
   */
  protected $output;


  public function __construct($name = NULL, EventDispatcherInterface $dispatcher) {
    $this->dispatcher = $dispatcher;
    parent::__construct($name);
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setName('pull')
      ->setDescription('Pull the site locally.');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;
    $successStyle = new OutputFormatterStyle('black', 'green');
    $this->output->getFormatter()->setStyle('success', $successStyle);
    $warningStyle = new OutputFormatterStyle('black', 'yellow');
    $this->output->getFormatter()->setStyle('warning', $warningStyle);
    $this->fs = new Filesystem();
    if (!$this->fs->exists('.siteSync.yml')) {
      $output->writeln("The site has not yet been initialized. Run the init command");
      return;
    }
    $configuration = Yaml::parseFile('.siteSync.yml');
    $typeObjectEvent = new GetSourceClassEvent($configuration);
    $this->dispatcher->dispatch($typeObjectEvent, SiteSyncEvents::GET_SOURCE_CLASS);
    $type = $typeObjectEvent->getSourceObject();
    $type->pull($this->input, $this->output);
  }

}
