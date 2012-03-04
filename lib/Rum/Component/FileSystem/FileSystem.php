<?php

namespace Rum\Component\FileSystem;

use Rum\Component\FileSystem\FileSystemDirectoryCreateException;
use Rum\Component\FileSystem\FileSystemDirectoryWritableException;

class FileSystem {

  public function checkDir($directory) {
    $return = FALSE;

    if (is_dir($directory)) {
      if (!is_writable($directory)) {
        throw new FileSystemDirectoryWritableException($directory);
      }

      $return = TRUE;
    }

    return $return;
  }
  
  public function createDir($directory) {
    $return = drush_op('mkdir', $directory);
    if ($return) {
      drush_log(dt('Created %directory', array('%directory' => $directory)), 'success');
    } else {
      throw new FileSystemDirectoryCreateException($directory);
    }

    return $return; 
  }
  
  public function checkFile($file) {
    if (is_file($file)) {
      return TRUE;
    }

    return FALSE;
  }
 
  public function createFile($file, $contents) {
    $tmp_file = drush_save_data_to_temp_file($contents);

    if ($tmp_file) {
      if (copy($tmp_file, $file)) {
        return TRUE;
      } else {
        // @throw copy failed
      }
    } else {
      // @throw tmpfile failed
    }
  }

  public function removeFile($file) {
    if (file_exists($file)) {
      if (is_dir($file)) {
        drush_delete_dir($file);
      }
      else {
        drush_op('unlink', $file);
        unlink($file);
      }
    }
  }
}