<?php

namespace EclipseGc\SiteSync\Action;

use EclipseGc\SiteSync\Configuration;
use Symfony\Component\Console\Output\OutputInterface;

trait SshDirectoryCheckTrait {

  use RunProcess;

  protected function checkRemoteDirectory(OutputInterface $output, Configuration $configuration) {
    $directory = $configuration->get("{$configuration->get('source')}.remote_directory");
    // @todo this whole directory bit is wrong.
    if ($configuration->hasValue('multisite') && $configuration->get('multisite') === "yes") {
      $directory .= "/sites/{$configuration->get("{$configuration->get('source')}.remote_site_directory")}";
    }
    $run = "ssh -q {$configuration->get("{$configuration->get('source')}.ssh_login")} [[ ! -d {$directory} ]] && exit -1 || exit 0;";
    $process = $this->startProcess($output, $run, NULL, NULL, NULL, 300);
    if ($process->isSuccessful()) {
      $output->writeln("<success>$directory found on remote server</success>");
    }
    else {
      $output->writeln("<error>The $directory directory was not found on the remote server</error>");
    }
    return $process;
  }

}
