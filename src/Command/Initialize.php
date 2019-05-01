<?php

namespace EclipseGc\SiteSync\Command;

use EclipseGc\SiteSync\Dispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class Initialize extends Command {

  /**
   * The siteSync dispatcher.
   *
   * @var \EclipseGc\SiteSync\Dispatcher
   */
  protected $dispatcher;

  public function __construct($name = NULL, Dispatcher $dispatcher) {
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
    $configuration = $this->dispatcher->getConfiguration();
    if ($configuration->hasValues()) {
      $output->writeln(".siteSync.yml file already exists. Edit the file directly to change your configuration.");
      return;
    }
    $helper = $this->getHelper('question');
    $type = new ChoiceQuestion("Site Type:", $this->dispatcher->getSources());
    $local_directory_name = new Question("Local subdirectory in which to store the downloaded site? (Will be created if it does not exist)");

    $configuration->set('type', $helper->ask($input, $output, $type));
    $sourceObject = $this->dispatcher->getSourceObject($configuration);
    foreach ($sourceObject->getQuestions() as $key => $question) {
      $configuration->set($key, $helper->ask($input, $output, $question));
    }
    $configuration->set('local_directory_name', $helper->ask($input, $output, $local_directory_name));
    $environment = new ChoiceQuestion("Environment Type:", $this->dispatcher->getEnvironments($output, $configuration));
    $configuration->set('environment', $helper->ask($input, $output, $environment));
    $environment = $this->dispatcher->getEnvironmentObject($sourceObject, $configuration);
    foreach ($environment->getQuestions() as $key => $question) {
      $configuration->set($key, $helper->ask($input, $output, $question));
    }
    $configuration->save();
  }

}
