<?php


namespace EclipseGc\SiteSync\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

class SubCommandTest extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setName('sub:command:test')
      ->setDescription('Testing nested commands');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $successStyle = new OutputFormatterStyle('black', 'green');
    $output->getFormatter()->setStyle('success', $successStyle);
    $warningStyle = new OutputFormatterStyle('black', 'yellow');
    $output->getFormatter()->setStyle('warning', $warningStyle);
    $helper = $this->getHelper('question');
    $ssh = $helper->ask($input, $output, new Question("SSH Login?: "));
    $process = new Process(['ssh', $ssh]);
    $process->run();
    if (!$process->isSuccessful()) {
      $output->writeln("<error>Failed to login</error>");
    }
    $directory = $helper->ask($input, $output, new Question("Remote directory to check exists: "));
    $command = ['ssh', '-q', $ssh];
    $command[] = "[[ ! -d $directory ]] && exit -1 || exit 0";
    $process->setCommandLine($command);
    $process->run();

    if (!$process->isSuccessful()) {
      $output->writeln("<error>Directory does not exist.</error>");
    }
    else {
      $output->writeln("<success>Directory exists.</success>");
    }
  }

}
