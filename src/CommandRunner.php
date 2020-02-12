<?php


namespace EclipseGc\SiteSync;


use EclipseGc\SiteSync\Action\RunProcess;
use Symfony\Component\Process\Process;

class CommandRunner {

  use RunProcess;

  public function getCommand(string $command, string ...$args) : Process {

  }

  protected function getCommandObject() {
    return new class (){

    };
  }

}