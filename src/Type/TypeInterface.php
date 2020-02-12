<?php


namespace EclipseGc\SiteSync\Type;


use Symfony\Component\Console\Output\OutputInterface;

interface TypeInterface {

  public function getDumpCommands(OutputInterface $output, CommandRunner $runner) : array ;

  public function getProjectType() : string ;

  public function getCompatibleSources() : array ;

}