<?php

namespace Rum\Component\Rum;

use Rum\Component\Rum\RumDecorator;
use Rum\Component\Rum\FileSystem;

class RumInstaller extends RumDecorator {

  public function __construct($rum) {
    parent::__construct($rum);
  }

  public function install($profile) {
    $options['account-name'] = drush_prompt(dt('Enter the administrator (uid=1) account name'));
    $options['account-pass'] = drush_prompt(dt('Enter the administrator (uid=1) password'));
    $options['account-mail'] = drush_prompt(dt('Enter the administrator (uid=1) e-mail address'));
    $options['locale'] = drush_prompt(dt('Enter your desired locale'));
    $options['site-name'] = drush_prompt(dt('Enter the name of your site'));
    $options['site-mail'] = drush_prompt(dt('Enter the global mail address of your site'));

    // Setting the options as a drush command specific context so the site install 
    // routine picks it up.
    drush_set_context('specific', $options);

    // Determin the major version and launch version specific installation.
    drush_include_engine('drupal', 'site_install', drush_drupal_major_version());
    drush_core_site_install_version($profile, $options);

    drush_log(dt('Installation finished.'), 'success');
  }

}