<?php


namespace EclipseGc\SiteSync;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Configuration {

  /**
   * The configuration values.
   *
   * @var array
   */
  protected $values;

  /**
   * The file system object.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $fs;

  public function __construct(array $values) {
    $this->values = $values;
    $this->fs = new Filesystem();
  }

  public static function getFromFile() {
    $fs = new Filesystem();
    $values = [];
    if ($fs->exists('.siteSync.yml')) {
      $values = Yaml::parseFile('.siteSync.yml');
    }
    return new static($values);
  }

  public function getValues() {
    return $this->values;
  }

  public function hasValues() {
    return (bool) $this->values;
  }

  public function hasValue(string $key) {
    return !empty($this->values[$key]);
  }

  public function get(string $key) {
    if (isset($this->values[$key])) {
      return $this->values[$key];
    }
  }

  public function set(string $key, $value) {
    $this->values[$key] = $value;
  }

  public function save() {
    $this->fs->dumpFile('.siteSync.yml', Yaml::dump($this->values));
  }

}