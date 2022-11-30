<?php
/**
 * wunschliste
 * This script provides functions for the administrator.
 *
 * @author Fabian Faul (faullab.com)
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

// update title of page
$html->title .= " | $lang->admin_mode";

if(!$wl->checkToken('admin', $get_token)) {
  // token is not valid or no token
  // check if form has been sent and verify password
  if(isset($_POST['password'])) {
    $form_password = del_all(trim($_POST['password']));

    // create password hash
    $pwhash = hash('sha256', $form_password);

    // check if username and password match
    if($pwhash == $config->ADMIN_PASSWORD) {
      // create token
      $token = $wl->createToken('admin');

      header("Location: $config->PAGE_URL/admin.php?t=$token");
      die();
    }
    else {
      $html->content = "<div class=\"error\">$lang->invalidpassword</div>";
      $html->redirect = 'admin.php';
    }
  }
  else {
    // show login form
    $html->content .= "<form action=\"admin.php\" method=\"post\" id=\"form\"><table>";
    $html->content .= "<tr><td><input type=\"password\" name=\"password\" id=\"password\" value=\"password\"></td></tr>";
    $html->content .= "<tr><td><input type=\"submit\" value=\"$lang->submit\" /></td></tr>";
    $html->content .= "</table></form>";
  }
}
else {
  // user is logged in; perform actions
  // update timestamp of token
  $wl->updateToken('admin', $get_token);

  if($get_action == 'adduser') {
    // action: add user
    $html->content .= "<h2>$lang->newuser</h2>";

    // check if form has been submitted
    if(isset($_POST['username']) and isset($_POST['fullname']) and isset($_POST['password'])) {
      // process form
      $form_username = del_all(trim($_POST['username']));
      $form_fullname = del_all(trim($_POST['fullname']));
      $form_password = del_all(trim($_POST['password']));

      // create password hash
      $pwhash = hash('sha256', $form_password);

      // add user to database
      $wl->addUser($form_username, $form_fullname, $pwhash);

      $html->content .= "<div class=\"message\">$lang->msg_entrysaved</div>";

      // show form with data again
      $html->content .= "<form><table>";
      $html->content .= "<tr><td><label for=\"username\">$lang->username:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"username\" id=\"username\" value=\"$form_username\" disabled=\"disabled\"></td></tr>";
      $html->content .= "<tr><td><label for=\"fullname\">$lang->fullname:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"fullname\" id=\"fullname\" value=\"$form_fullname\" disabled=\"disabled\"></td></tr>";
      $html->content .= "<tr><td><label for=\"password\">$lang->password:</label></td>";
      $html->content .= "<td><input type=\"password\" name=\"password\" id=\"password\" value=\"$pwhash\" disabled=\"disabled\"></td></tr>";
      $html->content .= "</table><form>";

      $html->content .= "<div class=\"navigation\"><a href=\"admin.php?t=$get_token\">&lt;&nbsp;$lang->backtomenu</a></div>";
    }
    else {
      // show form to add new user
      $html->content .= "<form action=\"admin.php?t=$get_token&a=adduser\" method=\"post\" id=\"form\"><table>";
      $html->content .= "<tr><td><label for=\"username\">$lang->username:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"username\" id=\"username\"></td></tr>";
      $html->content .= "<tr><td><label for=\"fullname\">$lang->fullname:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"fullname\" id=\"fullname\"></td></tr>";
      $html->content .= "<tr><td><label for=\"password\">$lang->password:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"password\" id=\"password\"></td></tr>";
      $html->content .= "<tr><td></td><td><input type=\"submit\" value=\"$lang->save\" /></td></tr>";
  		$html->content .= "</table></form>";

      $html->content .= "<div class=\"navigation\"><a href=\"admin.php?t=$get_token\">&lt;&nbsp;$lang->backtomenu</a></div>";
    }
  }
  elseif($get_action == 'deluser') {
    // action: delete user
    $html->content .= "<h2>$lang->deleteuser</h2>";

    // check if form has been submitted
    if(isset($_POST['user']) and isset($_POST['confirm'])) {
      // process form
      $form_user = del_all(trim($_POST['user']));
      $form_confirm = del_all(trim($_POST['confirm']));

      if($form_confirm == 'yes') {
        // delete user and all of its entries
        if($wl->delUser($form_user, 'name'))
          $html->content .= "<div class=\"message\">$lang->msg_userdeleted</div>";
        else
          $html->content .= "<div class=\"error\">$lang->msg_usernotdeleted</div>";
      }
      else {
        $html->content .= "<div class=\"error\">$lang->msg_usernotdeleted</div>";
      }

      $html->content .= "<div class=\"navigation\"><a href=\"admin.php?t=$get_token\">&lt;&nbsp;$lang->backtomenu</a></div>";
      $html->redirect = "admin.php?t=$get_token";
    }
    else {
      // show form to select and delete user
      $html->content .= "<form action=\"admin.php?t=$get_token&a=deluser\" method=\"post\" id=\"form\"><table>";
      $html->content .= "<tr><td><label for=\"user\">$lang->user:</label></td>";
      $html->content .= "<td><select name=\"user\" id=\"user\">";
      // get list of users
      foreach ($wl->getUserList() as $user) {
        $html->content .= "<option value=\"$user\">$user</option>";
      }
      $html->content .= "</select></td></tr>";
      $html->content .= "<tr><td>$lang->confirmdelete</td><td><input type=\"radio\" id=\"yes\" name=\"confirm\" value=\"yes\"> $lang->confirmdelete_yes<br><input type=\"radio\" id=\"no\" name=\"confirm\" value=\"no\" checked> $lang->confirmdelete_no</td></tr>";
      $html->content .= "<tr><td></td><td><input type=\"submit\" value=\"$lang->delete\" /></td></tr>";
  		$html->content .= "</table></form>";

      $html->content .= "<div class=\"navigation\"><a href=\"admin.php?t=$get_token\">&lt;&nbsp;$lang->backtomenu</a></div>";
    }
  }
  elseif($get_action == 'addcat') {
    // action: add category
    $html->content .= "<h2>$lang->newcategory</h2>";

    // check if form has been submitted
    if(isset($_POST['title'])) {
      // process form
      $form_title = del_all(trim($_POST['title']));

      // add category to database
      if($wl->addCategory($form_title))
        $html->content .= "<div class=\"message\">$lang->msg_categorycreated</div>";
      else
        $html->content .= "<div class=\"error\">$lang->msg_categorynotcreated</div>";

      $html->content .= "<div class=\"navigation\"><a href=\"admin.php?t=$get_token\">&lt;&nbsp;$lang->backtomenu</a></div>";
      $html->redirect = "admin.php?t=$get_token";
    }
    else {
      // show form to add new category
      $html->content .= "<form action=\"admin.php?t=$get_token&a=addcat\" method=\"post\" id=\"form\"><table>";
      $html->content .= "<tr><td><label for=\"title\">$lang->title:</label></td>";
      $html->content .= "<td><input type=\"text\" name=\"title\" id=\"title\"></td></tr>";
      $html->content .= "<tr><td></td><td><input type=\"submit\" value=\"$lang->save\" /></td></tr>";
  		$html->content .= "</table></form>";

      $html->content .= "<div class=\"navigation\"><a href=\"admin.php?t=$get_token\">&lt;&nbsp;$lang->backtomenu</a></div>";
    }

  }
  elseif($get_action == 'delcat') {
    // action: delete category
    $html->content .= "<h2>$lang->deletecategory</h2>";

    // check if form has been submitted
    if(isset($_POST['cid']) and isset($_POST['confirm'])) {
      // process form
      $form_cid = del_all(trim($_POST['cid']));
      $form_confirm = del_all(trim($_POST['confirm']));

      if($form_confirm == 'yes') {
        // delete category and all entries that belong to it
        if($wl->delCategory($form_cid))
          $html->content .= "<div class=\"message\">$lang->msg_categorydeleted</div>";
        else
          $html->content .= "<div class=\"error\">$lang->msg_categorynotdeleted</div>";
      }
      else {
        $html->content .= "<div class=\"error\">$lang->msg_categorynotdeleted</div>";
      }

      $html->content .= "<div class=\"navigation\"><a href=\"admin.php?t=$get_token\">&lt;&nbsp;$lang->backtomenu</a></div>";
      $html->redirect = "admin.php?t=$get_token";
    }
    else {
      // show form to select and delete category
      $html->content .= "<form action=\"admin.php?t=$get_token&a=delcat\" method=\"post\" id=\"form\"><table>";
      $html->content .= "<tr><td><label for=\"category\">$lang->category:</label></td>";
      $html->content .= "<td><select name=\"cid\" id=\"category\">";
      // get list of users
      foreach ($wl->getCategories() as $category) {
        $html->content .= "<option value=\"{$category['cid']}\">{$category['title']}</option>";
      }
      $html->content .= "</select></td></tr>";
      $html->content .= "<tr><td>$lang->confirmdelete</td><td><input type=\"radio\" id=\"yes\" name=\"confirm\" value=\"yes\"> $lang->confirmdelete_yes<br><input type=\"radio\" id=\"no\" name=\"confirm\" value=\"no\" checked> $lang->confirmdelete_no</td></tr>";
      $html->content .= "<tr><td></td><td><input type=\"submit\" value=\"$lang->delete\" /></td></tr>";
  		$html->content .= "</table></form>";

      $html->content .= "<div class=\"navigation\"><a href=\"admin.php?t=$get_token\">&lt;&nbsp;$lang->backtomenu</a></div>";
    }
  }
  elseif($get_action == 'cleartok') {
    // action: clear all tokens
    $html->content .= "<div class=\"message\">$lang->msg_tokenscleared</div>";
    // clear tokens
    $wl->clearAllToken();

    $html->content .= "<div class=\"navigation\"><a href=\"admin.php?t=$get_token\">&lt;&nbsp;$lang->backtolist</a></div>";
    $html->redirect = "admin.php?t=$get_token";
  }
  else {
    // action: show menu
    $html->content .= "<ul class=\"menu\">";
    $html->content .= "<li><a href=\"admin.php?a=adduser&t=$get_token\">$lang->newuser</a></li>";
    $html->content .= "<li><a href=\"admin.php?a=deluser&t=$get_token\">$lang->deleteuser</a></li>";
    $html->content .= "<li><a href=\"admin.php?a=addcat&t=$get_token\">$lang->newcategory</a></li>";
    $html->content .= "<li><a href=\"admin.php?a=delcat&t=$get_token\">$lang->deletecategory</a></li>";
    $html->content .= "<li><a href=\"admin.php?a=cleartok&t=$get_token\">$lang->cleartokens</a></li>";
    $html->content .= "</ul>";
  }
}

$html->make();

?>