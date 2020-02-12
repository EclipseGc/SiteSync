<?php

namespace EclipseGc\SiteSync\Action;

use Symfony\Component\Console\Output\OutputInterface;

trait RemoteDatabaseCommandViaSsh {

  use RunProcess;

  public function command(OutputInterface $output, string $command, string $ssh_login, string $database, string $user, string $password, string $host) {
    if (!in_array($command, ['mysql', 'mysqldump'])) {
      throw new \LogicException(sprintf("Allowed commands are only mysql or mysqldump. Command '%s' was passed.", $command));
    }
    if ($command === 'mysqldump') {
      // @todo add port with a default.
      $process = $this->startProcess($output, "ssh $ssh_login \"mysqldump $database -u $user -p$password -h $host | gzip\" | gunzip > db_export.sql", NULL, NULL, NULL, NULL);
    }
    elseif ($command === 'mysql') {
//      $this->startProcess($output, "ssh $ssh_login \"mysql ")
    }
    return $process;
  }

}
