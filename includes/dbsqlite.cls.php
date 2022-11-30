<?php
/**
 * wunschliste
 * @author Fabian Faul (faullab.com)
 */

if(!defined('INC_DB')){
  define('INC_DB', true);

  /**
   * CDBSQLite
   * This class extends the PHP SQLite3 class with an automatic check for an existing database.
   *
   * @category class
   * @uses SQLite3
   */
  class CDBSQLite extends SQLite3 {
    public function __construct($dbname) {
      $dbname = preg_replace('/[^a-z0-9:;,_!\? \.\-|\r|\n]/i', '', $dbname);
      $file = "data/".$dbname.".db";

      $this->open($file, $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    }


    // find valid id in table
    // input argument: $SQLString = "SELECT <id> FROM <table> ORDER BY <id> ASC";
    public function newID($SQL) {
      // get all existing IDs from table
      $res = $this->query($SQL);

      $exist = array();
      while($row = $res->fetchArray()) {
        $exist[] = $row[0];
      }

      if(!empty($exist)) {
        // test if IDs existent
        $id = 0;
        $found = false;
        while(!$found) {
          $id++;
          if(!in_array($id, $exist, true)) {
            $found = true;
          }
        }
      }
      else
        $id = 1;

      return $id;
    }

  };
}
?>