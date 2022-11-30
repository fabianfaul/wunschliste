<?php
/**
 * wunschliste
 * @author Fabian Faul (faullab.com)
 */

if(!defined('INC_LANG')){
  define('INC_LANG', true);

  /**
   * CLang (de)
   * This class provides the German text fragments.
   *
   * @category class
   */
  class CLang {
		var $lang = 'de';

    // general
		var $page_title = 'Wunschliste';
    var $page_title_withname = 'Wunschliste von';

    var $edit_mode = 'Bearbeiten';
    var $admin_mode = 'Verwaltung';
    var $nolist = 'Keine Liste gefunden.';
    var $noentries = 'Keine Einträge.';

    // session
    var $invalidsession = 'Ungültige Session.';
    var $invalidpassword = 'Ungültiges Passwort.';
    var $user = 'Benutzer';
    var $username = 'Nutzername';
    var $fullname = 'Vor- und Nachname';
    var $password = 'Passwort';
    var $hello = 'Hallo';

    // navigation
    var $backtomenu = 'Zurück zum Menü';
    var $backtolist = 'Zurück zur Übersicht';
    var $newuser = 'Neuer Nutzer';
    var $deleteuser = 'Lösche Nutzer';
    var $newcategory = 'Neue Kategorie';
    var $deletecategory = 'Lösche Kategorie';
    var $newentry = 'Neuer Eintrag';
    var $deleteentry = 'Lösche Eintrag';
    var $cleartokens = 'Lösche Tokens';
    var $submit = 'Absenden';
    var $save = 'Speichern';
    var $delete = 'Löschen';
    var $confirmdelete = 'Sicher löschen?';
    var $confirmdelete_yes = 'Ja, löschen.';
    var $confirmdelete_no = 'Nein, nicht löschen.';

    // messages
    var $msg_deleteentry = 'Eintrag löschen?';
    var $msg_entrynotdeleted = 'Eintrag konnte nicht gelöscht werden.';
    var $msg_entrydeleted = 'Eintrag erfolgreich gelöscht.';
    var $msg_entrynotsaved = 'Eintrag konnte nicht gespeichert werden.';
    var $msg_entrysaved = 'Eintrag erfolgreich gespeichert.';
    var $msg_categorynotcreated = 'Kategorie konnte nicht erstellt werden.';
    var $msg_categorycreated = 'Kategorie wurde erstellt.';
    var $msg_categorynotdeleted = 'Kategorie wurde nicht gelöscht.';
    var $msg_categorydeleted = 'Kategorie und alle zugehörigen Einträge wurden gelöscht.';
    var $msg_usernotdeleted = 'Nutzer wurde nicht gelöscht.';
    var $msg_userdeleted = 'Nutzer und alle zugehörigen Einträge wurden gelöscht.';
    var $msg_tokenscleared = 'Alle Tokens wurden gelöscht.';

    // entry/category details
    var $title = 'Name';
    var $description = 'Beschreibung';
    var $link = 'Link';
    var $link_name = 'Link-Text';
    var $link_url = 'Link';
    var $category = 'Kategorie';
  }

  $lang = new CLang;
}
?>