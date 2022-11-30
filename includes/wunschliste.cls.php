<?php
/**
 * wunschliste
 * @author Fabian Faul (faullab.com)
 */

if(!defined('INC_WUNSCHLISTE')){
  define('INC_WUNSCHLISTE', true);

  require('config.php');
  require('dbsqlite.cls.php');
  require('lib.inc.php');

  // load language data
  if(is_file("lang/$config->LANGUAGE.lang.php"))
    include("lang/$config->LANGUAGE.lang.php");
  else
    throw new Exception('Language file does not exist.');

  /**
   * CWunschliste
   * Class containing all access to the database and structures the content printed to the screen.
   *
   * @category class
   * @uses CDBSQLite
   */
  class CWunschliste {
    var $db;

    public function __construct($dbname) {
      // open database
      $this->db = new CDBSQLite($dbname);
    }


    /**
     * initializeDB
     * Drop existing tables and initialize database.
     * Tables: categories, entries, users
     *
     * @param none
     * @return none
     */
    public function initializeDB() {
      $sql = "DROP TABLE IF EXISTS categories";
      $this->db->exec($sql)
        or die('DATABASE ERROR!');

      $sql = "CREATE TABLE categories (
                cid INT NOT NULL,
                title VARCHAR(50) NOT NULL,
                PRIMARY KEY (cid)
              )";
	  	$this->db->exec($sql)
        or die('DATABASE ERROR!');


      $sql = "DROP TABLE IF EXISTS entries";
      $this->db->exec($sql)
        or die('DATABASE ERROR!');

      $sql = "CREATE TABLE entries (
                eid INT NOT NULL,
                uid INT NOT NULL,
                cid INT NOT NULL,
                title VARCHAR(50) NOT NULL,
                description TEXT,
                link_name VARCHAR(200),
                link_url VARCHAR(200),
                PRIMARY KEY (eid)
              )";
	  	$this->db->exec($sql)
        or die('DATABASE ERROR!');


      $sql = "DROP TABLE IF EXISTS users";
      $this->db->exec($sql)
        or die('DATABASE ERROR!');

      $sql = "CREATE TABLE users (
                uid INT NOT NULL,
                name VARCHAR(50) NOT NULL,
                fullname VARCHAR(50) NOT NULL,
                password CHAR(256) NOT NULL,
                PRIMARY KEY (uid)
              )";
	  	$this->db->exec($sql)
        or die('DATABASE ERROR!');


      $sql = "DROP TABLE IF EXISTS tokens";
      $this->db->exec($sql)
        or die('DATABASE ERROR!');

      $sql = "CREATE TABLE tokens (
                uid INT NOT NULL,
                token CHAR(40) NOT NULL,
                timestamp INT UNSIGNED NOT NULL,
                PRIMARY KEY (uid)
              )";
	  	$this->db->exec($sql)
        or die('DATABASE ERROR!');
    }


    /**
     * addUser
     * Add new user to database.
     *
     * @param name
     * @param fullname
     * @return success
     */
    public function addUser($name, $fullname, $password) {
      // get valid ID for new user
      $SQLString = "SELECT uid FROM users
                    ORDER BY uid ASC";
      $uid = $this->db->newID($SQLString);

      // write user data to table
      $sql = "INSERT INTO users (uid, name, fullname, password)
                         VALUES ($uid, '$name', '$fullname', '$password')";
	  	return $this->db->exec($sql)
        or die('DATABASE ERROR!');
    }


    /**
     * delUser
     * Delete user and all linked entries from database.
     *
     * @param user
     * @param type (id or name)
     * @return none
     */
    public function delUser($id, $type='id') {
      if($type == 'name') {
        // get ID of user
        $uid = $this->getUserID($id);
        }
        else {
          $uid = $id;
        }

      // delete user
      $sql = "DELETE FROM users
              WHERE uid = $uid";
	  	$ret1 = $this->db->exec($sql)
        or die('DATABASE ERROR!');

      // delete entries of user
      $sql = "DELETE FROM entries
              WHERE uid = $uid";
	  	$ret2 = $this->db->exec($sql)
        or die('DATABASE ERROR!');

      return ($ret1 and $ret2);
    }


    /**
     * validateUserPassword
     * Validate password of given user.
     *
     * @param user
     * @param type (id or name)
     * @return none
     */
    public function validateUserPassword($user, $password) {
      $sql = "SELECT COUNT(uid) FROM users
              WHERE name = '$user' AND password = '$password'";
      $res = $this->db->query($sql)
        or die('DATABASE ERROR!');
      return ($res->fetchArray()[0] == 1) ? true : false;
    }


    /**
     * getUserList
     * Get list of all users in database.
     *
     * @param none
     * @return list of users
     */
    public function getUserList() {
      // get names of users
      $sql = "SELECT name FROM users
              ORDER BY name ASC";
      $res = $this->db->query($sql)
        or die('DATABASE ERROR!');
      $entr = array();
      while($row = $res->fetchArray()) {
        $entr[] = $row[0];
      }
      return $entr;
    }


    /**
     * getUserID
     * Get user id from its name.
     *
     * @param user name
     * @return user id
     */
    public function getUserID($user) {
      if($this->validateUser($user)) {
        // get ID of user
        $sql = "SELECT uid FROM users
                WHERE name = '$user'";
        $res = $this->db->query($sql)
          or die('DATABASE ERROR!');
        return $res->fetchArray()[0];
      }
      else {
        return false;
      }
    }


    /**
     * getFullName
     * Get full name of user.
     *
     * @param user name
     * @return full name
     */
    public function getFullName($user) {
      if($this->validateUser($user)) {
        // get full name of user
        $sql = "SELECT fullname FROM users
                WHERE name = '$user'";
        $res = $this->db->query($sql)
          or die('DATABASE ERROR!');
        return $res->fetchArray()[0];
      }
      else {
        return false;
      }
    }


    /**
     * validateUser
     * Check if user exists and username is valid.
     *
     * @param user
     * @return valid
     */
    public function validateUser($user) {
      // get number of entries for given user
      $sql = "SELECT COUNT(uid) FROM users
              WHERE name = '$user'";
	  	$res = $this->db->query($sql)
        or die('DATABASE ERROR!');
      $cnt = $res->fetchArray()[0];
      return (intval($cnt) == 1) ? true : false;
    }


    /**
     * checkToken
     * Check if token is valid for user.
     *
     * @param user name
     * @param token
     * @return valid
     */
    public function checkToken($user, $token) {
      global $config;

      if($this->validateUser($user) or ($user == 'admin')) {
        if($user == 'admin')
          $uid = -1;
        else
          $uid = $this->getUserID($user);

        // check if one entry exists for given user/token combination
        $sql = "SELECT COUNT(uid) FROM tokens
                WHERE uid = $uid AND token = '$token'";
  	  	$res = $this->db->query($sql)
          or die('DATABASE ERROR!');
        $cnt = $res->fetchArray()[0];

        $exists = (intval($cnt) == 1) ? true : false;

        if($exists) {
          // get timestamp
          $sql = "SELECT timestamp FROM tokens
                  WHERE uid = $uid AND token = '$token'";
    	  	$res = $this->db->query($sql)
            or die('DATABASE ERROR!');
          $timestamp = intval($res->fetchArray()[0]);

          return ($exists and $timestamp <= time() + $config->SESSION_EXPIRE);
        }
        else {
          return false;
        }
      }
      else {
        return false;
      }
    }


    /**
     * createToken
     * Create new token after login. Delete old ones.
     *
     * @param user name
     * @return token
     */
    public function createToken($user) {
      if($this->validateUser($user) or ($user == 'admin')) {
        if($user == 'admin')
          $uid = -1;
        else
          $uid = $this->getUserID($user);

        // delete old token for user
        $sql = "DELETE FROM tokens
                WHERE uid = $uid";
  	  	$this->db->exec($sql)
          or die('DATABASE ERROR!');

        // calculate new token
        $new_token = calcToken($user);
        $new_timestamp = time();
        // create token for user
        $sql = "INSERT INTO tokens (uid, token, timestamp)
                           VALUES ($uid, '$new_token', $new_timestamp)";
  	  	$res = $this->db->exec($sql)
          or die('DATABASE ERROR!');

        if($res)
          return $new_token;
        else
          return false;
      }
      else {
        return false;
      }
    }


    /**
     * updateToken
     * Update timestamp of token with current UNIX time.
     *
     * @param user name
     * @param token
     * @return success
     */
    public function updateToken($user, $token) {
      if($this->checkToken($user, $token)) {
        if($user == 'admin')
          $uid = -1;
        else
          $uid = $this->getUserID($user);

        // check if one entry exists for given user/token combination
        $new_timestamp = time();
        $sql = "UPDATE tokens
                SET timestamp = $new_timestamp
                WHERE uid = $uid AND token = '$token'";
  	  	return $this->db->exec($sql)
          or die('DATABASE ERROR!');
      }
      else {
        return false;
      }
    }


    /**
     * clearAllToken
     * Delete all tokens from database.
     *
     * @param none
     * @return success
     */
    public function clearAllToken() {
      $sql = "DELETE FROM tokens
              WHERE NOT uid = -1";
	  	return $this->db->exec($sql)
        or die('DATABASE ERROR!');
    }


    /**
     * addCategory
     * Add new category that entries could belong to.
     *
     * @param title
     * @return success
     */
    public function addCategory($title) {
      // get valid ID for new user
      $SQLString = "SELECT cid FROM categories
                    ORDER BY cid ASC";
      $cid = $this->db->newID($SQLString);

      // write user data to table
      $sql = "INSERT INTO categories (cid, title)
                              VALUES ($cid, '$title')";
	  	return $this->db->exec($sql)
        or die('DATABASE ERROR!');
    }


    /**
     * delCategory
     * Delete category and all linked entries from database.
     *
     * @param category id
     * @return none
     */
    public function delCategory($cid) {
      // delete category
      $sql = "DELETE FROM categories
              WHERE cid = $cid";
	  	$ret1 = $this->db->exec($sql)
        or die('DATABASE ERROR!');

      // delete entries of category
      $sql = "DELETE FROM entries
              WHERE cid = $cid";
	  	$ret2 = $this->db->exec($sql)
        or die('DATABASE ERROR!');

      return ($ret1 and $ret2);
    }


    /**
     * getCategories
     * Get list of all existing categories.
     *
     * @param none
     * @return list of categories
     */
    public function getCategories() {
      $sql = "SELECT * FROM categories
              ORDER BY title ASC";
      $res = $this->db->query($sql)
        or die('DATABASE ERROR!');
      $cats = array();
      while($row = $res->fetchArray()) {
        $cats[] = $row;
      }
      return $cats;
    }


    /**
     * getCategoryTitle
     * Get name of specific category from its id.
     *
     * @param category id
     * @return category title
     */
    public function getCategoryTitle($cid) {
      $sql = "SELECT title FROM categories
              WHERE cid = $cid";
      return $this->db->query($sql)
        or die('DATABASE ERROR!');
    }


    /**
     * addEntry
     * Add new entry to database.
     *
     * @param category id
     * @param title
     * @param description (optional)
     * @param link_url (optional)
     * @param link_name (optional)
     * @return success
     */
    public function addEntry($uid, $cid, $title, $description='', $link_url='', $link_name='') {
      // get valid ID for new entry
      $SQLString = "SELECT eid FROM entries
                    ORDER BY eid ASC";
      $eid = $this->db->newID($SQLString);

      // write user data to table
      $sql = "INSERT INTO entries (eid, uid, cid, title, description, link_name, link_url)
                           VALUES ($eid, $uid, $cid, '$title', '$description', '$link_name', '$link_url')";
	  	return $this->db->exec($sql)
        or die('DATABASE ERROR!');
    }


    /**
     * delEntry
     * Delete entry from database.
     *
     * @param category id
     * @return success
     */
    public function delEntry($eid) {
      $sql = "DELETE FROM entries
              WHERE eid = $eid";
	  	return $this->db->exec($sql)
        or die('DATABASE ERROR!');
    }


    /**
     * delAllEntriesOfUser
     * Delete all entries that belong to given user.
     *
     * @param user id
     * @return success
     */
    public function delAllEntriesOfUser($uid) {
      $sql = "DELETE FROM entries
              WHERE uid = $uid";
	  	return $this->db->exec($sql)
        or die('DATABASE ERROR!');
    }


    /**
     * getEntry
     * Get single entry with id.
     *
     * @param entry id
     * @return entry
     */
    public function getEntry($eid) {
      $sql = "SELECT * FROM entries
              WHERE eid = $eid";
      $res = $this->db->query($sql)
        or die('DATABASE ERROR!');
      return $res->fetchArray();
    }


    /**
     * getEntries
     * Get all entries of specific user and category.
     *
     * @param user id
     * @param category id
     * @return list of entries
     */
    public function getEntries($uid, $cid) {
      $sql = "SELECT * FROM entries
              WHERE uid = $uid AND cid = $cid
              ORDER BY eid ASC";
      $res = $this->db->query($sql)
        or die('DATABASE ERROR!');
      $entr = array();
      while($row = $res->fetchArray()) {
        $entr[] = $row;
      }
      return $entr;
    }


    /**
     * print
     * Print full wishlist. Categories and entries in alphabethical order.
     *
     * @param user name
     * @return html code
     */
    public function print($user, $token='', $edit=false) {
      global $lang;
      $content = '';

      // get user id
      $uid = $this->getUserID($user);

      foreach($this->getCategories() as $category) {
        $entries = $this->getEntries($uid, $category['cid']);
        if(count($entries) >= 1 or $edit) {
          $content .= "<div class=\"category\">";
          $content .= "<h2>{$category['title']}";
          $content .= ($edit) ? "<span class=\"edit\"><a href=\"edit.php?u=$user&t=$token&a=add&c={$category['cid']}\">&nbsp;[+]</a></span>" : '';
          $content .= "</h2>";
          if(count($entries) == 0 and $edit) {
            $content .= "<span><i>$lang->noentries</i></span>";
          }
          foreach($entries as $entry) {
            $content .= "<p><span class=\"item\">{$entry['title']}</span>";
            $content .= ($edit) ? "<span class=\"edit\"><a href=\"edit.php?u=$user&t=$token&a=del&e={$entry['eid']}\">&nbsp;[-]</a></span>" : '';
            if(!empty($entry['description'])) {
              $content .= "<br><span class=\"description\">{$entry['description']}</span>";
            }
            if(!empty($entry['link_url'])) {
              if(empty($entry['link_name'])) {
                $content .= "<br><a href=\"{$entry['link_url']}\">$lang->link</a>";
              }
              else {
                $content .= "<br><a href=\"{$entry['link_url']}\">{$entry['link_name']}</a>";
              }
            }
            $content .= "</p>";
          }
          $content .= "</div>";
        }
      }

      return $content;
    }


    /**
     * printUsers
     * Print list of all users with their full name in alphabetical order.
     *
     * @return html code
     */
    public function printUsers() {
      $content = '';

      // get list of users
      $content .= "<ul class=\"menu\">";
      foreach ($this->getUserList() as $user) {
        $content .= "<li><a href=\"index.php/$user\">".$this->getFullName($user)."</a></li>";
      }
      $content .= "</ul>";

      return $content;
    }

  };

}
?>