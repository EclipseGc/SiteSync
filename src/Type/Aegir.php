<?php

namespace EclipseGc\SiteSync\Type;

use EclipseGc\SiteSync\Action\PrepareLocalDirectory;
use EclipseGc\SiteSync\Action\RsyncDirectory;
use EclipseGc\SiteSync\Action\SshDirectoryCheckTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class Aegir extends TypeBase {

  use SshDirectoryCheckTrait, RsyncDirectory {
    SshDirectoryCheckTrait::startProcess insteadof RsyncDirectory;
  }
  use PrepareLocalDirectory;

  public function getQuestions() : array {
    $questions = [];
    $questions['ssh_login'] = new Question('SSH login:');
    $questions['remote_directory'] = new Question("Platform directory:");
    $questions['composer_managed'] = new ChoiceQuestion("Is the platform managed by composer?", ["yes", "no"], "yes");
    $questions['remote_site_directory'] = new Question("Sites directory name:");
    $questions['local_directory_name'] = new Question("Local subdirectory in which to store the downloaded site? (Will be created if it does not exist)");
    $questions['local_mysql'] = new Question("Local mysql executable:");
    return $questions;
  }

  public function pull(InputInterface $input, OutputInterface $output) : void {
    $this->checkRemoteDirectory($output, $this->configuration);
    $this->prepLocalDirectory($output, $this, $this->configuration);
    $source = "{$this->configuration['ssh_login']}:{$this->configuration['remote_directory']}";
    $destination = $this->configuration['local_directory_name'];
    $this->rsyncDirectory($output, $source, $destination, $this->getExclusions());
    if ($this->configuration['composer_managed'] === "yes") {
      $source .= "/web";
      $destination .= "/web";
    }
    $source .= "/sites/{$this->configuration['remote_site_directory']}";
    if (!$this->getFileSystem()->exists($destination . "/sites")) {
      $this->getFileSystem()->mkdir($destination . "/sites");
    }
    $destination .="/sites/default";
    $this->rsyncDirectory($output, $source, $destination);
  }

  public function getLocalSettingsFileLocation(): string {
    if ($this->configuration['composer_managed'] === "yes") {
      return "{$this->configuration['local_directory_name']}/web/sites/default/settings.php";
    }
    return "{$this->configuration['local_directory_name']}/sites/default/settings.php";
  }

  protected function getExclusions() {
    $exclusions = !empty($this->configuration['exclusions']) ? $this->configuration['exclusions'] : [];
    if ($this->configuration['composer_managed'] === "yes") {
      $exclusions[] = "web/sites";
    }
    else {
      $exclusions[] = "sites";
    }
    return $exclusions;
  }


}
