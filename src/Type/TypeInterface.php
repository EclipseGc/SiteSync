<?php

namespace EclipseGc\SiteSync\Type;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface TypeInterface {

  /**
   * @return \Symfony\Component\Console\Question\Question[]
   */
  public function getQuestions() : array ;

  public function pull(InputInterface $input, OutputInterface $output) : void ;

  public function getProjectType() : string ;

  public function getLocalSettingsFileLocation() : string ;

  public function getDump(OutputInterface $output);

}
