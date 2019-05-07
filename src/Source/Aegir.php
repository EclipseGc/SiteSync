<?php

namespace EclipseGc\SiteSync\Source;

use EclipseGc\SiteSync\Action\DumpRemoteDatabaseViaSsh;
use EclipseGc\SiteSync\Action\PrepareLocalDirectory;
use EclipseGc\SiteSync\Action\RsyncDirectory;
use EclipseGc\SiteSync\Action\SshDirectoryCheckTrait;
use EclipseGc\SiteSync\Type\Drupal8;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class Aegir extends SourceBase {

  const ID = 'aegir';

  const LABEL = 'Aegir';

  const SERVICE_ID = 'sitesync.source.aegir';

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
    $directory = $this->configuration->get("aegir.remote_directory");
    // @todo this whole directory bit is wrong.
    if ($this->configuration->hasValue('aegir.composer_managed') && $this->configuration->get('aegir.composer_managed') === "yes") {
      $directory .= "/web";
    }
    $directory .= "/sites/{$this->configuration->get("aegir.remote_site_directory")}";
    $this->checkRemoteDirectory($output, $this->configuration->get('aegir.ssh_login'), $directory);
    $this->prepLocalDirectory($output, $this, $this->configuration);
    $source = "{$this->configuration->get('aegir.ssh_login')}:{$this->configuration->get('aegir.remote_directory')}";
    $destination = $this->configuration->get('local_directory_name');
    $this->rsyncDirectory($output, $source, $destination, $this->getExclusions());
    if ($this->configuration->get('aegir.composer_managed') === "yes") {
      $source .= "/web";
      $destination .= "/web";
    }
    $source .= "/sites/{$this->configuration->get('aegir.remote_site_directory')}";
    if (!$this->getFileSystem()->exists($destination . "/sites")) {
      $this->getFileSystem()->mkdir($destination . "/sites");
    }
    $destination .="/sites/default";
    $this->rsyncDirectory($output, $source, $destination);
    $this->getDump($output);
//    $this->dump($output, )
    // Get environment up and running.
    $environment = $this->dispatcher->getEnvironmentObject();
    $environment->init($input, $output);
    $environment->start($input, $output);
    $environment->importDb($input, $output);
  }

  public function getDocroot(): string {
    // TODO: Implement getDocroot() method.
  }

  public function getLocalSettingsFileLocation(): string {
    if ($this->configuration->get('aegir.composer_managed') === "yes") {
      return "{$this->configuration->get('local_directory_name')}/web/sites/default/settings.php";
    }
    return "{$this->configuration->get('local_directory_name')}/sites/default/settings.php";
  }

  protected function getExclusions() {
    $exclusions = !empty($this->configuration->hasValue('aegir.exclusions')) ? $this->configuration->get('aegir.exclusions') : [];
    if ($this->configuration->get('aegir.composer_managed') === "yes") {
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
    $this->dump($output, $this->configuration->get('aegir.ssh_login'), $options['db_name'], $options['db_user'], $options['db_passwd'], $options['db_host']);
    // Prep for import.
    $settings_backup_parts = $parts;
    $settings_backup_parts[] = 'aegir-settings.php';
    $this->getFileSystem()->copy($settings, implode(DIRECTORY_SEPARATOR, $settings_backup_parts));
    $this->getFileSystem()->remove($settings);
    $default_source = "{$this->configuration->get('aegir.ssh_login')}:{$this->configuration->get('aegir.remote_directory')}";
    $destination = $this->configuration->get('local_directory_name');
    if ($this->configuration->get('aegir.composer_managed') === "yes") {
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

  public static function getCompatibility(): array {
    return [
      Drupal8::ID,
    ];
  }


}
