<?php

namespace EclipseGc\SiteSync\Action;

use Symfony\Component\Console\Output\OutputInterface;

trait DumpRemoteDatabaseViaSsh {

  use RunProcess;

  public function dump(OutputInterface $output, string $ssh_login, string $database, string $user, string $password, string $host) {
    // @todo add port with a default.
    $this->startProcess($output, "ssh $ssh_login \"mysqldump $database -u $user -p$password -h $host | gzip\" | gunzip > db_export.sql", NULL, NULL, NULL, NULL);
  }

}
