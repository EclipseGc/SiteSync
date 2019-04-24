<?php


namespace EclipseGc\SiteSync\Command;


use EclipseGc\SiteSync\Event\GetTypeClassEvent;
use EclipseGc\SiteSync\Event\GetTypesEvent;
use EclipseGc\SiteSync\SiteSyncEvents;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
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
    $eventTypes = new GetTypesEvent();
    $this->dispatcher->dispatch($eventTypes, SiteSyncEvents::GET_TYPES);
    $type = new ChoiceQuestion("Site Type:", $eventTypes->getTypes());

    $configuration['type'] = $helper->ask($input, $output, $type);
    $typeObjectEvent = new GetTypeClassEvent($configuration);
    $this->dispatcher->dispatch($typeObjectEvent, SiteSyncEvents::GET_TYPE_CLASS);
    $typeObject = $typeObjectEvent->getTypeObject();
    foreach ($typeObject->getQuestions() as $key => $question) {
      $configuration[$key] = $helper->ask($input, $output, $question);
    }
    $this->fs->dumpFile('.siteSync.yml', Yaml::dump($configuration));
  }

}
