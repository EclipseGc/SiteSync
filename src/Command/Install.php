<?php


namespace EclipseGc\SiteSync\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;

class Install extends Command {

  /**
   * The filesystem object.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $fs;

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setName('install')
      ->setDescription('Install siteSync.');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->fs = new Filesystem();
    if ($this->fs->exists("/usr/local/bin/siteSync")) {
      throw new LogicException("siteSync is already installed.");
    }
    $executable = __DIR__ . '/../../bin/siteSync.php';
    if (!$this->fs->exists($executable)) {
      throw new LogicException(sprintf("Could not find siteSync.php file. Looked in %s.", $executable));
    }
    $this->fs->dumpFile("/usr/local/bin/siteSync", $this->getExecutableContents($executable));
    $this->fs->chmod("/usr/local/bin/siteSync", 0755);
  }

  protected function getExecutableContents(string $executable) {
    $finder = new PhpExecutableFinder();
    $php_path = $finder->find();
    return <<<EOF
#!/usr/bin/env bash

$php_path $executable $@
EOF;
  }

}
