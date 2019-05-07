<?php

namespace EclipseGc\SiteSync\Source;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface SourceInterface {

  /**
   * @return \Symfony\Component\Console\Question\Question[]
   */
  public function getQuestions() : array ;

  public function pull(InputInterface $input, OutputInterface $output) : void ;

  public function getDocroot() : string ;

  public function getLocalSettingsFileLocation() : string ;

  public function getDump(OutputInterface $output);

  public static function getCompatibility() : array ;

}
