<?php

namespace EclipseGc\SiteSync\EventSubscriber\Source;

use EclipseGc\SiteSync\Source\Drupal;

class DrupalSsh extends SourceSubscriberBase {

  public function getType() : string {
    return Drupal::ID;
  }

  public function getLabel() : string {
    return Drupal::LABEL;
  }

  public function getServiceId() : string {
    return Drupal::SERVICE_ID;
  }

  public function getCompatibility(string $type = NULL) : array {
    return Drupal::getCompatibility();
  }

}
