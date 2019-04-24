<?php


namespace EclipseGc\SiteSync\Action;


use Symfony\Component\Console\Output\OutputInterface;

trait SshDirectoryCheckTrait {

  use RunProcess;

  protected function checkRemoteDirectory(OutputInterface $output, array $configuration) {
    $directory = $configuration['remote_directory'];
    if ($configuration['multisite'] === "yes") {
      $directory .= "/sites/{$configuration['remote_site_directory']}";
    }
    $run = "ssh -q {$configuration['ssh_login']} [[ ! -d {$directory} ]] && exit -1 || exit 0;";
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
