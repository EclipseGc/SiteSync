<?php

namespace EclipseGc\SiteSync\Action;

use EclipseGc\SiteSync\Configuration;
use EclipseGc\SiteSync\Source\SourceInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait PrepareLocalDirectory {

  use GetFileSystem;

  protected function prepLocalDirectory(OutputInterface $output, SourceInterface $type, Configuration $configuration) {
    $local_directory_name = $configuration->get('local_directory_name');
    if (!$this->getFileSystem()->exists($local_directory_name)) {
      $this->getFileSystem()->mkdir($local_directory_name);
      $output->writeln("<success>The \"{$local_directory_name}\" directory was created.</success>");
    }
    else {
      $settings_location = $type->getLocalSettingsFileLocation();
      if ($this->getFileSystem()->exists($settings_location)) {
        $output->writeln("<warning>The settings.php file was not found in your local code base. This may or may not be problematic depending on the state of your local installation.</warning>");
        return;
      }
      $this->getFileSystem()->copy($settings_location, "settings.php");
      $output->writeln("<success>Your local settings.php file was backed up outside of the webroot.</success>");
    }
  }

}
