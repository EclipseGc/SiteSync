<?php


namespace EclipseGc\SiteSync\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Initialize extends Command {

  /**
   * The filesystem object.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $fs;

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
    $login = new Question('SSH login:');
    $remote_directory = new Question("Remote directory:");
    $multisite = new ChoiceQuestion("Multisite", ["yes", "no"], "no");
    if ($multisite === "yes") {
      $remote_site_directory = new Question("Remote subsite directory:");
    }
    $remote_db = new Question("Remote database name:");
    $local_mysql = new Question("Local mysql executable:");

    $configuration['ssh_login'] = $helper->ask($input, $output, $login);
    $configuration['remote_directory'] = $helper->ask($input, $output, $remote_directory);
    $configuration['multisite'] = $helper->ask($input, $output, $multisite);
    if ($multisite === "yes") {
      $configuration['remote_site_directory'] = $helper->ask($input, $output, $remote_site_directory);
    }
    $configuration['remote_db'] = $helper->ask($input, $output, $remote_db);
    $configuration['local_mysql'] = $helper->ask($input, $output, $local_mysql);

    $this->fs->dumpFile('.siteSync.yml', Yaml::dump($configuration));
  }

}
