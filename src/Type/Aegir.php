<?php

namespace EclipseGc\SiteSync\Type;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Aegir implements TypeInterface {

  public function getQuestions() {
    $questions = [];
    $questions['login'] = new Question('SSH login:');
    $questions['remote_directory'] = new Question("Platform directory:");
    $questions['remote_site_directory'] = new Question("Sites directory name:");
    $questions['local_mysql'] = new Question("Local mysql executable:");
    return $questions;
  }

  public function pull(InputInterface $input, OutputInterface $output) {
    // TODO: Implement pull() method.
  }

}
