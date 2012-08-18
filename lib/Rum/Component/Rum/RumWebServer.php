<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\Rum\Exception\RumClassTypeNotFound;
use Rum\Component\WebServer\WebServer;
use Rum\Component\FileSystem\FileSystem;

class RumWebServer extends RumDecorator {

  private $web_server;

  private $file_system;

  const RUM_HTTP_APACHE = 'Apache';

  const RUM_HTTP_NGINX = 'Nginx';

  function __construct($rum) {
    parent::__construct($rum);
    $this->checkSetting('rum-http-type');
    $class_name = drush_get_option('rum-http-type', '');
    $this->file_system = new FileSystem();
    switch ($class_name) {
      case self::RUM_HTTP_APACHE :
      case self::RUM_HTTP_NGINX :
        $this->web_server = WebServer::getInstance($class_name);
        break;
      default :
        throw new RumClassTypeNotFound($class_name, 'Webserver');
    }
    $settings = array('rum-http-port', 'rum-http-doc-root');
    $settings += $this->web_server->getSettings();
    foreach ($settings as $setting) {
      $this->checkSetting($setting);
    }
  }

  public function createVhost() {
    $port = drush_get_option('rum-http-port', '');
    $project_domain = $this->getProjectDomain();
    $web_dir = $this->getProjectDir() . '/' . $this->getDocumentRoot();
    $link = drush_get_option('rum-http-doc-root', '') . '/' . $this->getProjectName();
    $time = $this->getTime();
    if (!$this->file_system->checkFile($link)) {
      $this->file_system->createLink($web_dir, $link);
    }
    $this->web_server->createVhost($time, $port, $project_domain, $link);
  }

  public function removeVhost() {
    $link = drush_get_option('rum-http-doc-root', '') . '/' . $this->getProjectName();
    if ($this->file_system->checkFile($link)) {
      $this->file_system->removeFile($link);
    }
    $project_domain = $this->getProjectDomain();
    $this->web_server->removeVhost($project_domain);
  }

  public function restart() {
    $os = $this->getOs();
    $this->web_server->restart($os);
  }
}