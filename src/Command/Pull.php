<?php

namespace EclipseGc\SiteSync\Command;

use EclipseGc\SiteSync\Dispatcher;
use EclipseGc\SiteSync\Event\GetSourceObjectEvent;
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
   * @var \EclipseGc\SiteSync\Dispatcher
   */
  protected $dispatcher;

  /**
   * The filesystem object.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $fs;


  public function __construct($name = NULL, Dispatcher $dispatcher) {
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
    $successStyle = new OutputFormatterStyle('black', 'green');
    $output->getFormatter()->setStyle('success', $successStyle);
    $warningStyle = new OutputFormatterStyle('black', 'yellow');
    $output->getFormatter()->setStyle('warning', $warningStyle);

    $this->fs = new Filesystem();
    if (!$this->fs->exists('.siteSync.yml')) {
      $output->writeln("The site has not yet been initialized. Run the init command");
      return;
    }
    $source = $this->dispatcher->getSourceObject();
    $source->pull($input, $output);
  }

}
