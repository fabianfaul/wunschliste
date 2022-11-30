<?php
/**
 * wunschliste
 * This script creates and initializes the database.
 *
 * @author Fabian Faul (faullab.com)
 */

require('config.php');
require('includes/html.cls.php');
require('includes/wunschliste.cls.php');
require('includes/lib.inc.php');


$html = new CHtml();

if(file_exists('INITIALIZE')) {
  // create and initialize database
  $wl = new CWunschliste('wunschliste');
  $wl->initializeDB();

  // delete file 'INITIALIZE'
  unlink('INITIALIZE');

  $html->content .= "<div class=\"message\">Database has been initialized.</div>";
}
else {
  $html->content .= "<div class=\"error\">Database could not be initialized.</div>";
}

$html->make();

?>