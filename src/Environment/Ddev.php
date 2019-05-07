<?php

namespace EclipseGc\SiteSync\Environment;

use EclipseGc\SiteSync\Action\RunProcess;
use EclipseGc\SiteSync\Configuration;
use EclipseGc\SiteSync\Source\SourceInterface;
use EclipseGc\SiteSync\Type\TypeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class Ddev implements EnvironmentInterface {

  use RunProcess;

  public const ID = "ddev";

  public const LABEL = "DDEV";

  /**
   * @var \EclipseGc\SiteSync\Configuration
   */
  protected $configuration;

  /**
   * @var \EclipseGc\SiteSync\Type\TypeInterface
   */
  protected $type;

  /**
   * The source type.
   *
   * @var \EclipseGc\SiteSync\Source\SourceInterface
   */
  protected $source;

  /**
   * The file system.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $fs;

  public function __construct(Configuration $configuration, TypeInterface $type, SourceInterface $source) {
    $this->configuration = $configuration;
    $this->type = $type;
    $this->source = $source;
    $this->fs = new Filesystem();
  }

  public function getQuestions() : array {
    $questions = [];
    $cwd = getcwd();
    $dir = array_pop(explode(DIRECTORY_SEPARATOR, $cwd));
    $questions['project_name'] = new Question("Project name:", $dir);
    $questions['http_port'] = new Question("HTTP port:", 80);
    $questions['http_port']->setValidator(function ($value) {
      if (!is_numeric($value)) {
        throw new \RuntimeException("The http port number should be an integer.");
      }
      return $value;
    });
    $questions['https_port'] = new Question("HTTPS port:", 443);
    $questions['https_port']->setValidator($questions['http_port']->getValidator());
    return $questions;
  }

  public function init(InputInterface $input, OutputInterface $output) {
    if ($this->fs->exists(".ddev")) {
      $output->writeln("<warning>.ddev directory already exists, skipping ddev initialization.</warning>");
      return;
    }
    $docroot = $this->configuration->get('local_directory_name');
    if ($this->configuration->get("{$this->configuration->get('source')}.composer_managed") === 'yes') {
      $docroot .= DIRECTORY_SEPARATOR . "web";
    }
    $this->startProcess($output, "ddev config --docroot=$docroot --project-name={$this->configuration->get('ddev.project_name')} --project-type={$this->type->getProjectType()} --http-port={$this->configuration->get('ddev.http_port')} --https-port={$this->configuration->get('ddev.https_port')}");
  }

  public function start(InputInterface $input, OutputInterface $output) {
    $this->startProcess($output, "ddev start", NULL, NULL, NULL, NULL);
  }

  public function importDb(InputInterface $input, OutputInterface $output) {
    $this->startProcess($output, "ddev import-db --src=db_export.sql", NULL, NULL, NULL, NULL);
  }

}
