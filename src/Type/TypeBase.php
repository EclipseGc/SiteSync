<?php

namespace EclipseGc\SiteSync\Type;

abstract class TypeBase implements TypeInterface {

  /**
   * The siteSync configuration.
   *
   * @var array
   */
  protected $configuration;

  public function __construct(array $configuration) {
    $this->configuration = $configuration;
  }

}
