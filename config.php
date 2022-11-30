<?php
/**
 * wunschliste
 * @author Fabian Faul (faullab.com)
 */

if(!defined('INC_CONFIG')){
  define('INC_CONFIG', true);

  /**
   * CConfig
   * Container class for variables that control the general program behavior.
   *
   * @category class
   */
  class CConfig {
    var $LANGUAGE     = 'en';   // choose language (de, en)
    var $PAGE_URL     = 'https://wunschliste.example.org';   // URL to installation
    var $SHOWUNAME    = true;   // show full name of user in headline
    var $SHOWLIST     = true;   // show overview of available wishlists on startpage

    var $ADMIN_PASSWORD     = 'PASSWORD';   // administrator password, hash with sha256

    var $PATH_ROOT     = '/var/www/wunschliste/';   // absolute path to script root folder
    var $PATH_DATA     = 'data/';   // relative path from script root for database
    var $PATH_USER     = 'user/';   // relative path from script root for user data

    var $SESSION_EXPIRE = 600;   // session expires after x seconds
  };

  $config = new CConfig;

}
?>