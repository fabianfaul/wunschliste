<?php
/**
 * wunschliste
 * Online wishlist for adding items with a simple user interface.
 * This script provides functions for users to edit their list.
 *
 * @author Fabian Faul
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

// get parameters
$get_token = (isset($_GET['t'])) ? del_all(trim($_GET['t'])) : '';
$get_user = (isset($_GET['u'])) ? del_all(trim($_GET['u'])) : '';
$get_action = (isset($_GET['a'])) ? del_all(trim($_GET['a'])) : 'list';
$get_category = (isset($_GET['c'])) ? del_all(trim($_GET['c'])) : -1;
$get_entry = (isset($_GET['e'])) ? del_all(trim($_GET['e'])) : -1;

// update title of page
$html->title .= " | $lang->edit_mode";

if(!$wl->checkToken($get_user, $get_token)) {
  // token is not valid or no token
  // check if form has been sent and verify username and password
  if(isset($_POST['user']) and isset($_POST['password'])) {
    $form_user = del_all(trim($_POST['user']));
    $form_password = del_all(trim($_POST['password']));

    // create password hash
    $pwhash = hash('sha256', $form_password);

    // check if username and password match
    if($wl->validateUserPassword($form_user, $pwhash)) {
      // create token
      $token = $wl->createToken($form_user);

      header("Location: $config->PAGE_URL/edit.php?u=$form_user&t=$token");
      die();
    }
    else {
      $html->content = "<div class=\"error\">$lang->invalidpassword</div>";
      $html->redirect = 'edit.php';
    }
  }
  else {
    // show login form
    $html->content .= "<form action=\"edit.php\" method=\"post\" id=\"form\"><table>";
    $tmp_userval = (empty($get_user)) ? $lang->user : $get_user;
    $html->content .= "<tr><td><input type=\"text\" name=\"user\" id=\"user\" value=\"$tmp_userval\"></td></tr>";
    $html->content .= "<tr><td><input type=\"password\" name=\"password\" id=\"password\" value=\"password\"></td></tr>";
    $html->content .= "<tr><td><input type=\"submit\" value=\"$lang->submit\" /></td></tr>";
    $html->content .= "</table></form>";
  }
}
else {
  // user is logged in; perform actions
  // update timestamp of token
  $wl->updateToken($get_user, $get_token);

  if(!$wl->validateUser($get_user)) {
    // no valid user given
    $html->title = '';
    $html->content = $lang->nolist;
  }
  elseif($get_action == 'add') {
    // action: add entry to list
    $html->content .= "<h2>$lang->newentry</h2>";

    // check if form has been submitted
    if(isset($_POST['category']) and isset($_POST['title']) and isset($_POST['description']) and isset($_POST['link_url']) and isset($_POST['link_name'])) {
      // process form
      $form_category = del_all(trim($_POST['category']));
      $form_title = del_all(trim($_POST['title']));
      $form_description = del_all(trim($_POST['description']));
      $form_link_url = del_all(trim($_POST['link_url']));
      $form_link_name = del_all(trim($_POST['link_name']));

      // add entry to database
      $uid = $wl->getUserID($get_user);
      if($wl->addEntry($uid, $form_category, $form_title, $form_description, $form_link_url, $form_link_name))
        $html->content .= "<div class=\"message\">$lang->msg_entrysaved</div>";
      else
        $html->content .= "<div class=\"error\">$lang->msg_entrynotsaved</div>";

      // show form with data again
      $html->content .= "<form><table>";
      $html->content .= "<tr><td><label for=\"category\">$lang->category:</label></td>";
      $html->content .= "<td><select name=\"category\" id=\"category\" disabled=\"disabled\">";
        foreach($wl->getCategories() as $category) {
          $html->content .= "<option value=\"{$category['cid']}\"";
          $html->content .= ($category['cid'] == $form_category) ? "selected=\"selected\"" : '';
          $html->content .= ">{$category['title']}</option>";
        }
      $html->content .= "</select></td></tr>";
      $html->content .= "<tr><td><label for=\"title\">$lang->title*:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"title\" id=\"title\" value=\"$form_title\" disabled=\"disabled\"></td></tr>";
      $html->content .= "<tr><td><label for=\"description\">$lang->description:</label></td>";
      $html->content .= "<td><textarea name=\"description\" id=\"description\" disabled=\"disabled\">$form_description</textarea></td></tr>";
      $html->content .= "<tr><td><label for=\"link_url\">$lang->link_url:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"link_url\" id=\"link_url\" value=\"$form_link_url\" disabled=\"disabled\"></td></tr>";
      $html->content .= "<tr><td><label for=\"link_name\">$lang->link_name:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"link_name\" id=\"link_name\" value=\"$form_link_name\" disabled=\"disabled\"></td></tr>";
      $html->content .= "</table><form>";

      $html->content .= "<div class=\"navigation\"><a href=\"edit.php?u=$get_user&t=$get_token\">&lt;&nbsp;$lang->backtolist</a></div>";
    }
    else {
      // show form to add new entry
      $html->content .= "<form action=\"edit.php?u=$get_user&t=$get_token&a=add\" method=\"post\" id=\"form\"><table>";
      $html->content .= "<tr><td><label for=\"category\">$lang->category:</label></td>";
      $html->content .= "<td><select name=\"category\" id=\"category\">";
        foreach($wl->getCategories() as $category) {
          $html->content .= "<option value=\"{$category['cid']}\"";
          $html->content .= ($category['cid'] == $get_category) ? "selected=\"selected\"" : '';
          $html->content .= ">{$category['title']}</option>";
        }
      $html->content .= "</select></td></tr>";
      $html->content .= "<tr><td><label for=\"title\">$lang->title:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"title\" id=\"title\"></td></tr>";
      $html->content .= "<tr><td><label for=\"description\">$lang->description<span class=\"tooltip\">*<span class=\"tooltiptext\">optional</span></span>:</label></td>";
      $html->content .= "<td><textarea name=\"description\" id=\"description\"></textarea></td></tr>";
      $html->content .= "<tr><td><label for=\"link_url\">$lang->link_url<span class=\"tooltip\">*<span class=\"tooltiptext\">optional</span></span>:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"link_url\" id=\"link_url\"></td></tr>";
      $html->content .= "<tr><td><label for=\"link_name\">$lang->link_name<span class=\"tooltip\">*<span class=\"tooltiptext\">optional</span></span>:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"link_name\" id=\"link_name\"></td></tr>";
      $html->content .= "<tr><td></td><td><input type=\"submit\" value=\"$lang->save\" /></td></tr>";
  		$html->content .= "</table></form>";

      $html->content .= "<div class=\"navigation\"><a href=\"edit.php?u=$get_user&t=$get_token\">&lt;&nbsp;$lang->backtolist</a></div>";
    }

  }
  elseif($get_action == 'del') {
    // action: delete entry from list
    $html->content .= "<h2>$lang->deleteentry</h2>";

    // check if form has been submitted
    if(isset($_POST['eid'])) {
      // process form
      $form_eid = del_all(trim($_POST['eid']));

      // get entry details
      $uid = $wl->getUserID($get_user);
      $entry = $wl->getEntry($form_eid);

      // check if entry to delete belongs to current user
      if(($entry['eid'] == $form_eid) and ($entry['uid'] == $uid)) {
        // delete entry from database
        $wl->delEntry($form_eid);
        $html->content .= "<div class=\"message\">$lang->msg_entrydeleted</div>";
      }
      else {
        $html->content .= "<div class=\"error\">$lang->msg_entrynotdeleted</div>";
      }

      $html->content .= "<div class=\"navigation\"><a href=\"edit.php?u=$get_user&t=$get_token\">&lt;&nbsp;$lang->backtolist</a></div>";
      $html->redirect = "edit.php?u=$get_user&t=$get_token";
    }
    else {
      // get details of entry
      $entry = $wl->getEntry($get_entry);

      // show form with details of entry to delete it
      $html->content .= "<form action=\"edit.php?u=$get_user&t=$get_token&a=del\" method=\"post\" id=\"form\"><table>";
      $html->content .= "<input type=\"hidden\" name=\"eid\" id=\"eid\" value=\"$get_entry\">";
      $html->content .= "<tr><td><label for=\"category\">$lang->category:</label></td>";
      $html->content .= "<td><select name=\"category\" id=\"category\" disabled=\"disabled\">";
        foreach($wl->getCategories() as $category) {
          $html->content .= "<option value=\"{$category['cid']}\"";
          $html->content .= ($category['cid'] == $entry['cid']) ? " selected=\"selected\"" : '';
          $html->content .= ">{$category['title']}</option>";
        }
      $html->content .= "</select></td></tr>";
      $html->content .= "<tr><td><label for=\"title\">$lang->title:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"title\" id=\"title\" value=\"{$entry['title']}\" disabled=\"disabled\"></td></tr>";
      $html->content .= "<tr><td><label for=\"description\">$lang->description:</label></td>";
      $html->content .= "<td><textarea name=\"description\" id=\"description\" disabled=\"disabled\">{$entry['description']}</textarea></td></tr>";
      $html->content .= "<tr><td><label for=\"link_url\">$lang->link_url:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"link_url\" id=\"link_url\" value=\"{$entry['link_url']}\" disabled=\"disabled\"></td></tr>";
      $html->content .= "<tr><td><label for=\"link_name\">$lang->link_name:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"link_name\" id=\"link_name\" value=\"{$entry['link_name']}\" disabled=\"disabled\"></td></tr>";
      $html->content .= "<tr><td></td><td><input type=\"submit\" value=\"$lang->delete\" /></td></tr>";
      $html->content .= "</table></form>";

      $html->content .= "<div class=\"navigation\"><a href=\"edit.php?u=$get_user&t=$get_token\">&lt;&nbsp;$lang->backtolist</a></div>";
    }
  }
  else {
    // action: show list
    $html->content .= "<div class=\"message\">$lang->hello {$wl->getFullName($get_user)}.</div>";
    $html->content .= $wl->print($get_user, $get_token, $edit=true);
  }
}

$html->make();

?>