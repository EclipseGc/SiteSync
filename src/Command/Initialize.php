<?php


namespace EclipseGc\SiteSync\Command;


use EclipseGc\SiteSync\Event\GetTypeClassEvent;
use EclipseGc\SiteSync\Event\GetTypesEvent;
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
    $eventTypes = new GetTypesEvent();
    $this->dispatcher->dispatch(SiteSyncEvents::GET_TYPES, $eventTypes);
    $type = new ChoiceQuestion("Site Type:", $eventTypes->getTypes());

    $configuration['type'] = $helper->ask($input, $output, $type);
    $typeObjectEvent = new GetTypeClassEvent($configuration['type']);
    $this->dispatcher->dispatch(SiteSyncEvents::GET_TYPE_CLASS, $typeObjectEvent);
    $typeObject = $typeObjectEvent->getTypeObject();
    foreach ($typeObject->getQuestions() as $key => $question) {
      $configuration[$key] = $helper->ask($input, $output, $question);
    }

//    $configuration['ssh_login'] = $helper->ask($input, $output, $login);
//    $configuration['remote_directory'] = $helper->ask($input, $output, $remote_directory);
//    $configuration['multisite'] = $helper->ask($input, $output, $multisite);
//    if ($multisite === "yes") {
//      $configuration['remote_site_directory'] = $helper->ask($input, $output, $remote_site_directory);
//    }
//    $configuration['remote_db'] = $helper->ask($input, $output, $remote_db);
//    $configuration['local_mysql'] = $helper->ask($input, $output, $local_mysql);

//    $this->fs->dumpFile('.siteSync.yml', Yaml::dump($configuration));
  }

}
