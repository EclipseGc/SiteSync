<?php


namespace EclipseGc\SiteSync\Action;


use Symfony\Component\Filesystem\Filesystem;

trait GetFileSystem {

  /**
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $fs;

  protected function getFileSystem() {
    if (!$this->fs) {
      $this->fs = new Filesystem();
    }
    return $this->fs;
  }

}
