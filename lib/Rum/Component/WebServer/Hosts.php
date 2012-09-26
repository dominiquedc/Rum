<?php

namespace Rum\Component\WebServer;

use Rum\Component\FileSystem\FileSystem;
use Rum\Component\WebServer\Exception\RumHostsFileDoesNotExist;

class Hosts {

  protected static $instance = NULL;

  public function getInstance() {
    if (!self::$instance) {
      $class_name = __CLASS__;
      self::$instance = new $class_name;
    }

    return self::$instance;
  }

  public function addHostsEntry($project_domain) {
    $hosts_file = drush_get_option('rum-hosts-file', '');
    drush_log(dt('Adding host entry to !file', array('!file' => $hosts_file)), 'status');
    $hosts_lines = explode("\n", file_get_contents($hosts_file));
    $host_available = FALSE;
    foreach ($hosts_lines as $line) {
      if (preg_match("/" . $project_domain . "/", $line)) {
        $host_available = TRUE;
      }
    }
    
    // Remove stray empty lines
    $hosts_lines = array_filter($hosts_lines);

    if (!$host_available) {
      $hosts_lines[] = "127.0.0.1\t" . $project_domain;
      // Use exec because the lines might contain % which we really do not need here.
      exec("sudo sh -c 'echo \"" . implode("\n", $hosts_lines) . "\" > /etc/hosts'");
      drush_log(dt('Entry %project_domain added to hosts file', array('%project_domain' => $project_domain)), 'success');
    } else {
      drush_log(dt('Entry %project_domain already in hosts file', array('%project_domain' => $project_domain)), 'warning');
    }
  }

  public function removeHostsEntry($project_domain) {
    $hosts_file = drush_get_option('rum-hosts-file', '');
    drush_log(dt('Removing host entry from !file', array('!file' => $hosts_file)), 'status');
    $hosts_lines = explode("\n", file_get_contents($hosts_file));
    $host_available = FALSE;
    foreach ($hosts_lines as $delta => $host) {
      if (preg_match("/" . $project_domain . "/", $host)) {
        unset($hosts_lines[$delta]);
        $host_available = TRUE;
      }
    }

    // Remove stray empty lines
    $hosts_lines = array_filter($hosts_lines);

    if ($host_available) {
      exec("sudo sh -c 'echo \"" . implode("\n", $hosts_lines) . "\" > /etc/hosts'");
      drush_log(dt('Entry %project_domain removed from hosts file', array('%project_domain' => $project_domain)), 'success');
    } else {
      drush_log(dt('Entry %project_domain was not found in hosts file', array('%project_domain' => $project_domain)), 'warning');
    }
  }

  public function getSettings() {
    return array('rum-hosts-file');
  }
}