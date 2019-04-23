<?php

namespace EclipseGc\SiteSync\Type;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Drupal implements TypeInterface {

  public function getQuestions() {
    $questions = [];
    $questions['login'] = new Question('SSH login:');
    $questions['remote_directory'] = new Question("Remote directory:");
    $questions['local_mysql'] = new Question("Local mysql executable:");
    return $questions;
  }

  public function pull(InputInterface $input, OutputInterface $output) {
    // TODO: Implement pull() method.
  }


}