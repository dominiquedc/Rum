<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\FileSystem\FileSystem;

class RumFileSystem extends RumDecorator {
  
  private $file_system;
  
  private $project_dir;

  public function __construct(Rum $rum) {
    parent::__construct($rum);
    $this->file_system = new FileSystem();
  }

  public function createWorkSpace() {
    $workspace = $this->getWorkspace();
    $this->createDirectory($workspace);
  }
  
  public function createProjectDir() {
    $project_dir = $this->getProjectDir();
    $this->createDirectory($project_dir);
    $project_db_dir = $project_dir . '/db';
    $this->createDirectory($project_db_dir);
    $project_web_dir = $project_dir . '/www';
    $this->createDirectory($project_web_dir);
    $this->project_dir = $project_dir;
  }

  private function createDirectory($directory) {
    $result = $this->file_system->checkDir($directory);

    if (!$result) {
      $create = drush_confirm(dt('Directory %directory does not exist, do you want me to create it?', array('%directory' => $directory)));
      if ($create) {
        $result = $this->file_system->createDir($directory);
      } else {
        $result = drush_user_abort('Aborting...');
      }
    }    
  }

}