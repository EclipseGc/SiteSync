<?php

namespace EclipseGc\SiteSync\Action;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

trait RunProcess {

  /**
   * @param string $command
   *
   * @param string|null $dir
   *
   * @return \Symfony\Component\Process\Process
   */
  protected function startProcess(OutputInterface $output, string $command, string $dir = NULL, $env = NULL, $input = NULL, $timout = 60): Process {
    $process = Process::fromShellCommandline($command, $dir, $env, $input, $timout);
    $process->start();
    if ($output->isVerbose()) {
      foreach ($process as $type => $data) {
        $output->writeln($data);
      }
    }
    $process->wait(function ($type, $buffer) use ($output) {
      if (Process::ERR === $type) {
        $output->writeln("<error>$buffer</error>");
      } else {
        $output->writeln($buffer);
      }
    });
    return $process;
  }

}
