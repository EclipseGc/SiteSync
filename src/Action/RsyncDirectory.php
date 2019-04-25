<?php


namespace EclipseGc\SiteSync\Action;


use Symfony\Component\Console\Output\OutputInterface;

trait RsyncDirectory {

  use RunProcess;

  protected function rsyncDirectory(OutputInterface $output, string $source, string $destination, $exclusions = [], $options = "ac", $delete = TRUE) {
    $exclude_text = '';
    foreach ($exclusions as $exclusion) {
      $exclude_text .= " --exclude=$exclusion";
    }
    if ($output->isVerbose()) {
      $options .= "v";
    }
    $delete ? $delete = '--delete': $delete = '';
    $command = "rsync -$options $delete $exclude_text $source/ $destination";
    return $this->startProcess($output, $command, NULL, NULL, NULL, NULL);
  }

}
