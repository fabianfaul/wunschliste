<?php
/**
 * wunschliste
 * Online wishlist with a simple user interface.
 *
 * @author Fabian Faul (faullab.com)
 * @copyright Copyright (c) 2022, Fabian Faul
 * @version 1.0
 */

require('config.php');
require('includes/html.cls.php');
require('includes/wunschliste.cls.php');
require('includes/lib.inc.php');

// load language data
if(is_file("lang/$config->LANGUAGE.lang.php"))
  include("lang/$config->LANGUAGE.lang.php");
else
  throw new Exception('Language file does not exist.');


$wl = new CWunschliste('wunschliste');
$html = new CHtml();

// get user
$user = (isset($_GET['u'])) ? del_all(trim($_GET['u'])) : '';

if(!$wl->validateUser($user)) {
  // no valid user given
  $html->title = '';
  $html->content = $lang->nolist;

  // display overview of all lists
  if($config->SHOWLIST)
    $html->content = $wl->printUsers();
}
else {
  // display full wishlist of chosen user
  if($config->SHOWUNAME)
    $html->title = $lang->page_title_withname.' '.$wl->getFullName($user);
  $html->content .= $wl->print($user);
}

$html->make();

?>