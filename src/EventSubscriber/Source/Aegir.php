<?php

namespace EclipseGc\SiteSync\EventSubscriber\Source;

use EclipseGc\SiteSync\Source\Aegir as AegirType;

class Aegir extends SourceSubscriberBase {

  public function getType() : string {
    return AegirType::ID;
  }

  public function getLabel() : string {
    return AegirType::LABEL;
  }

  public function getServiceId() : string {
    return AegirType::SERVICE_ID;
  }

  public function getCompatibility(string $type = NULL) : array {
    return AegirType::getCompatibility();
  }

}
