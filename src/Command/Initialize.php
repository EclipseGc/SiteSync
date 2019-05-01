<?php


namespace EclipseGc\SiteSync\Command;


use EclipseGc\SiteSync\Event\GetEnvironmentObjectEvent;
use EclipseGc\SiteSync\Event\GetEnvironmentsEvent;
use EclipseGc\SiteSync\Event\GetSourceClassEvent;
use EclipseGc\SiteSync\Event\GetSourcesEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Initialize extends Command {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * The filesystem object.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $fs;

  public function __construct($name = NULL, EventDispatcherInterface $dispatcher) {
    $this->dispatcher = $dispatcher;
    parent::__construct($name);
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setName('init')
      ->setDescription('Initialize a new site for siteSync.');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->fs = new Filesystem();
    if ($this->fs->exists('.siteSync.yml')) {
      $output->writeln(".siteSync.yml file already exists. Edit the file directly to change your configuration.");
      return;
    }
    $configuration = [];
    $helper = $this->getHelper('question');
    $eventTypes = new GetSourcesEvent();
    $this->dispatcher->dispatch($eventTypes, SiteSyncEvents::GET_SOURCES);
    $type = new ChoiceQuestion("Site Type:", $eventTypes->getTypes());
    $local_directory_name = new Question("Local subdirectory in which to store the downloaded site? (Will be created if it does not exist)");

    $configuration['type'] = $helper->ask($input, $output, $type);
    $typeObjectEvent = new GetSourceClassEvent($configuration);
    $this->dispatcher->dispatch($typeObjectEvent, SiteSyncEvents::GET_SOURCE_CLASS);
    $typeObject = $typeObjectEvent->getSourceObject();
    foreach ($typeObject->getQuestions() as $key => $question) {
      $configuration[$key] = $helper->ask($input, $output, $question);
    }
    $configuration['local_directory_name'] = $helper->ask($input, $output, $local_directory_name);
    $environmentTypes = new GetEnvironmentsEvent($configuration, $output);
    $this->dispatcher->dispatch($environmentTypes, SiteSyncEvents::GET_ENVIRONMENTS);
    $environment = new ChoiceQuestion("Environment Type:", $environmentTypes->getAvailableEnvironments());
    $configuration['environment'] = $helper->ask($input, $output, $environment);
    $environmentObjectEvent = new GetEnvironmentObjectEvent($configuration, $typeObject);
    $this->dispatcher->dispatch($environmentObjectEvent, SiteSyncEvents::GET_ENVIRONMENT_OBJECT);
    $environment = $environmentObjectEvent->getEnvironmentObject();
    foreach ($environment->getQuestions() as $key => $question) {
      $configuration[$key] = $helper->ask($input, $output, $question);
    }
    $this->fs->dumpFile('.siteSync.yml', Yaml::dump($configuration));
  }

}
