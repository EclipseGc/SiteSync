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
  protected $values = [];

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

  public function hasValues() : bool {
    return (bool) $this->values;
  }

  public function hasValue(string $key) : bool {
    $reference = $this->values;
    foreach (explode('.', $key) as $valueAsKey) {
      if (!isset($reference[$valueAsKey])) {
        return FALSE;
      }
      if (is_array($reference[$valueAsKey])) {
        $reference = $reference[$valueAsKey];
        continue;
      }
      return !empty($reference[$valueAsKey]);
    }
  }

  public function get(string $key) : string {
    $reference = $this->values;
    foreach (explode('.', $key) as $valueAsKey) {
      if (!isset($reference[$valueAsKey])) {
        throw new \RuntimeException(sprintf("The key '%s' was not found in the configuration object.", $key));
      }
      if (is_array($reference[$valueAsKey])) {
        $reference = $reference[$valueAsKey];
        continue;
      }
      return $reference[$valueAsKey];
    }
  }

  public function set(string $key, string $value) {
    foreach (array_reverse(explode('.', $key)) as $valueAsKey) {
      $value = [$valueAsKey => $value];
    }
    $this->values = array_merge_recursive($this->values, $value);
  }

  public function save() {
    $this->fs->dumpFile('.siteSync.yml', Yaml::dump($this->values));
  }

}
