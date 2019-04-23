<?php

namespace EclipseGc\SiteSync\Type;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface TypeInterface {

  /**
   * @return \Symfony\Component\Console\Question\Question[]
   */
  public function getQuestions();

  public function pull(InputInterface $input, OutputInterface $output);

}