<?php

namespace EclipseGc\SiteSync\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class Pull extends Command {

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

  protected $exclusions = [
    '_archived'
  ];

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
    $this->fs = new Filesystem();
    if (!$this->fs->exists('.siteSync.yml')) {
      $output->writeln("The site has not yet been initialized. Run the init command");
      return;
    }
    $configuration = Yaml::parseFile('.siteSync.yml');
    if (!$this->getSshCheck($configuration)->isSuccessful()) {
      return;
    }
    $this->prepLocalDirectory($configuration);
    $this->rsyncRemoteSite($configuration);
  }

  protected function getSshCheck(array $configuration) {
    $directory = $configuration['remote_directory'];
    if ($configuration['multisite'] === "yes") {
      $directory .= "/sites/{$configuration['remote_site_directory']}";
    }
    $run = "ssh -q {$configuration['ssh_login']} [[ ! -d {$directory} ]] && exit -1 || exit 0;";
    $process = $this->startProcess($run);
    if ($process->isSuccessful()) {
      $this->output->writeln("<success>$directory found on remote server</success>");
    }
    else {
      $this->output->writeln("<error>The $directory directory was not found on the remote server</error>");
    }
    return $process;
  }

  protected function prepLocalDirectory(array $configuration) {
    if (!$this->fs->exists("html")) {
      $this->fs->mkdir("html");
      $this->output->writeln("<success>The \"html\" directory was created.</success>");
    }
    else {
      $this->fs->copy("html/sites/default/settings.php", "settings.php");
      $this->output->writeln("<success>Your local settings.php file was backed up outside of the webroot.</success>");
    }
  }

  protected function rsyncRemoteSite(array $configuration) {
    $directory = $configuration['remote_directory'];
    $exclusions = $this->exclusions;
    if (isset($configuration['exclusions'])) {
      $exclusions += $configuration['exclusions'];
    }
    if ($configuration['multisite'] === "yes") {
      $exclusions[] = 'sites';
    }
    $exclude_text = '';
    foreach ($exclusions as $exclusion) {
      $exclude_text .= " --exclude=$exclusion";
    }
    $options = "-ac";
    if ($this->output->isVerbose()) {
      $options .= "v";
    }
    $command = "rsync $options --delete $exclude_text {$configuration['ssh_login']}:$directory/ html";
    return $this->startProcess($command);
  }

  /**
   * @param string $command
   *
   * @param string|null $dir
   *
   * @return \Symfony\Component\Process\Process
   */
  protected function startProcess(string $command, string $dir = NULL): Process {
    $process = Process::fromShellCommandline($command, $dir, null, null, 300);
    $process->start();
    if ($this->output->isVerbose()) {
      foreach ($process as $type => $data) {
        $this->output->writeln($data);
      }
    }
    $process->wait(function ($type, $buffer) {
      if (Process::ERR === $type) {
        $this->output->writeln("<error>$buffer</error>");
      } else {
        $this->output->writeln($buffer);
      }
    });
    return $process;
  }

}