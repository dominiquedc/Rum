<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\Drush\Drush;

class RumDrush extends RumDecorator {

  protected $drush;

  public function __construct(Rum $rum) {
    parent::__construct($rum);
    $this->drush = new Drush();
    $settings = $this->drush->getSettings();
    foreach ($settings as $setting) {
      $this->checkSetting($setting);
    }
  }

  public function createDrush() {
    $environment = $this->getEnvironment();
    $domain_name = $this->getProjectDomain();
    $www_dir = $this->getProjectDir() . '/www';
    $project_name = $this->getProjectName();
    $this->drush->createDrush($environment, $project_name, $domain_name, $www_dir);
  }

  public function removeDrush() {
    
  }
  
}