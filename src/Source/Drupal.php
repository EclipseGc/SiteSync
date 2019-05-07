<?php

namespace EclipseGc\SiteSync\Source;

use EclipseGc\SiteSync\Action\SshDirectoryCheckTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Drupal extends SourceBase {

  const ID = 'drupal_ssh';

  const LABEL = 'Drupal across SSH';

  const SERVICE_ID = 'sitesync.source.drupal_ssh';

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

  public function getDocroot(): string {
    // TODO: Implement getDocroot() method.
  }

  public function getLocalSettingsFileLocation(): string {
    // TODO: Implement getLocalSettingsFileLocation() method.
  }

  public function getDump(OutputInterface $output) {

  }

  public static function getCompatibility(): array {
    return [];
  }

}
