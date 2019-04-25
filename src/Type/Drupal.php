<?php

namespace EclipseGc\SiteSync\Type;

use EclipseGc\SiteSync\Action\SshDirectoryCheckTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Drupal extends TypeBase {

  use SshDirectoryCheckTrait;

  public function getQuestions() : array {
    $questions = [];
    $questions['login'] = new Question('SSH login:');
    $questions['remote_directory'] = new Question("Remote directory:");
    $questions['local_mysql'] = new Question("Local mysql executable:");
    return $questions;
  }

  public function pull(InputInterface $input, OutputInterface $output) : void {
    // TODO: Implement pull() method.
  }

  public function getProjectType(): string {
    return 'drupal8';
  }

  public function getLocalSettingsFileLocation(): string {
    // TODO: Implement getLocalSettingsFileLocation() method.
  }

  public function getDump(OutputInterface $output) {

  }

}
