<?php
/**
 * wunschliste
 * @author Fabian Faul (faullab.com)
 */

if(!defined('INC_HTML')){
  define('INC_HTML', true);

  require('config.php');

  // load language data
  if(is_file("lang/$config->LANGUAGE.lang.php"))
    include("lang/$config->LANGUAGE.lang.php");
  else
    throw new Exception('Language file does not exist.');

  /**
   * CHtml
   * Container class that contains the template and prints it alongside the content when all scripts have been executed.
   *
   * @category class
   */
  class CHtml {
    var $title;
    var $redirect;
    var $content;


    public function __construct() {
      global $lang;
      $this->title = $lang->page_title;
      $this->redirect = '';
			$this->content = '';
    }

    // print HTML code
    public function make() {
      global $config;
?>
      <!doctype html>
      <html lang="en">
        <head>
          <meta http-equiv="content-type" content="text/html; charset=UTF-8">
          <?php if(!empty($this->redirect)) { ?>
            <meta http-equiv="refresh" content="2; url=<?=$this->redirect?>" />
          <?php } ?>
          <title><?=$this->title ?></title>

          <meta name="robots" content="noindex, nofollow">

          <link rel="stylesheet" type="text/css" href="<?=$config->PAGE_URL?>/css/template.css">
          <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        </head>
        <body>
          <div id="header">
            <h1><?=$this->title ?></h1>
          </div>
          <div id="main">
            <?=$this->content ?>
          </div>
        </body>
      </html>
<?php
    }

  };
}
?>