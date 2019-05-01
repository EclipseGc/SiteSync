<?php

namespace EclipseGc\SiteSync\Source;

use EclipseGc\SiteSync\Action\DumpRemoteDatabaseViaSsh;
use EclipseGc\SiteSync\Action\PrepareLocalDirectory;
use EclipseGc\SiteSync\Action\RsyncDirectory;
use EclipseGc\SiteSync\Action\SshDirectoryCheckTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class Aegir extends SourceBase {

  use SshDirectoryCheckTrait, RsyncDirectory, DumpRemoteDatabaseViaSsh {
    SshDirectoryCheckTrait::startProcess insteadof RsyncDirectory;
    SshDirectoryCheckTrait::startProcess insteadof DumpRemoteDatabaseViaSsh;
  }
  use PrepareLocalDirectory;

  public function getQuestions() : array {
    $questions = [];
    $questions['ssh_login'] = new Question('SSH login:');
    $questions['remote_directory'] = new Question("Platform directory:");
    $questions['composer_managed'] = new ChoiceQuestion("Is the platform managed by composer?", ["yes", "no"], "yes");
    $questions['remote_site_directory'] = new Question("Sites directory name:");
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
    $this->getDump($output);
//    $this->dump($output, )
    // Get environment up and running.
    $environment = $this->getEnvironmentObject($input, $output);
    $environment->init($input, $output);
    $environment->start($input, $output);
    $environment->importDb($input, $output);
  }

  public function getProjectType(): string {
    return 'drupal8';
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

  public function getDump(OutputInterface $output) {
    $settings = $this->getLocalSettingsFileLocation();
    $parts = explode(DIRECTORY_SEPARATOR, $settings);
    array_pop($parts);
    $drush_rc_parts = $parts;
    $drush_rc_parts[] = "drushrc.php";
    $drushrc_location = implode(DIRECTORY_SEPARATOR, $drush_rc_parts);
    if (!$this->getFileSystem()->exists($drushrc_location)) {
      $output->writeln("<warning>Missing drushrc.php at $drushrc_location.</warning>");
      throw new \RuntimeException("Missing drushrc.php");
    }
    include $drushrc_location;
    $this->dump($output, $this->configuration['ssh_login'], $options['db_name'], $options['db_user'], $options['db_passwd'], $options['db_host']);
    // Prep for import.
    $settings_backup_parts = $parts;
    $settings_backup_parts[] = 'aegir-settings.php';
    $this->getFileSystem()->copy($settings, implode(DIRECTORY_SEPARATOR, $settings_backup_parts));
    $this->getFileSystem()->remove($settings);
    $default_source = "{$this->configuration['ssh_login']}:{$this->configuration['remote_directory']}";
    $destination = $this->configuration['local_directory_name'];
    if ($this->configuration['composer_managed'] === "yes") {
      $default_source .= "/web";
      $destination .= "/web";
    }
    $destination .= "/sites/default";
    $default_source .= "/sites/default/";
    $default_settings_source = $default_source;
    $default_settings_source .= "default.settings.php";
    $this->startProcess($output, "rsync -ac $default_settings_source $destination");
    $default_services_source = $default_source;
    $default_services_source .= "default.services.yml";
    $this->startProcess($output, "rsync -ac $default_services_source $destination");
    $this->getFileSystem()->copy("$destination/default.settings.php", $settings);
  }

}
