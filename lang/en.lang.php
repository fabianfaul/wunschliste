<?php
/**
 * wunschliste
 * @author Fabian Faul (faullab.com)
 */

if(!defined('INC_LANG')){
  define('INC_LANG', true);

  /**
   * CLang (en)
   * This class provides the English text fragments.
   *
   * @category class
   */
  class CLang {
		var $lang = 'en';

    // general
		var $page_title = 'Wishlist';
    var $page_title_withname = 'Wishlist of';

    var $edit_mode = 'Edit-Mode';
    var $admin_mode = 'Administration';
    var $nolist = 'No list found.';
    var $noentries = 'No entries.';

    // session
    var $invalidsession = 'Invalid Session.';
    var $invalidpassword = 'Ivalid Password.';
    var $user = 'User';
    var $username = 'Username';
    var $fullname = 'Full Name';
    var $password = 'Password';
    var $hello = 'Hello';

    // navigation
    var $backtomenu = 'Back to Menu';
    var $backtolist = 'Back to List';
    var $newuser = 'Add User';
    var $deleteuser = 'Delete User';
    var $newcategory = 'Add Category';
    var $deletecategory = 'Delete Category';
    var $newentry = 'Add Entry';
    var $deleteentry = 'Delete Entry';
    var $cleartokens = 'Clear Tokens';
    var $submit = 'Submit';
    var $save = 'Save';
    var $delete = 'Delete';
    var $confirmdelete = 'Are you sure to delete?';
    var $confirmdelete_yes = 'Yes, delete.';
    var $confirmdelete_no = 'No, don\'t do it.';

    // messages
    var $msg_deleteentry = 'Delete entry?';
    var $msg_entrynotdeleted = 'Entry could not be deleted.';
    var $msg_entrydeleted = 'Entry successfully deleted.';
    var $msg_entrynotsaved = 'Entry could not be saved.';
    var $msg_entrysaved = 'Entry successfully saved.';
    var $msg_categorynotcreated = 'Category could not be created.';
    var $msg_categorycreated = 'Category has been created.';
    var $msg_categorynotdeleted = 'Category not deleted.';
    var $msg_categorydeleted = 'Category and all corresponding entries deleted.';
    var $msg_usernotdeleted = 'User not deleted.';
    var $msg_userdeleted = 'User and all corresponding entries deleted.';
    var $msg_tokenscleared = 'All tokens have been cleared.';

    // entry/category details
    var $title = 'Title';
    var $description = 'Description';
    var $link = 'Link';
    var $link_name = 'Link Name';
    var $link_url = 'Link URL';
    var $category = 'Category';
  }

  $lang = new CLang;
}
?>