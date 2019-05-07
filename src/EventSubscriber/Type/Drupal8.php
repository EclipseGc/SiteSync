<?php

namespace EclipseGc\SiteSync\EventSubscriber\Type;

use EclipseGc\SiteSync\Type\Drupal8 as Drupal8Type;

class Drupal8 extends TypeSubscriberBase {

  public function getType() {
    return Drupal8Type::ID;
  }

  public function getLabel() {
    return Drupal8Type::LABEL;
  }

  public function getServiceId() {
    return Drupal8Type::SERVICE_ID;
  }

}
