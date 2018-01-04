<?php

class Utils {
  private static $locales = array('es'=>'es_ES', 'en'=>'en_US', 'pt'=>'pt_BR');
  public static $default_lang = 'es';

  public static function getLocale($lang) {
     return (isset(self::$locales[$lang])) ? self::$locales[$lang] : $locales[self::$default_lang]; }

  public static function safeDate($dateString) {
    $time = strtotime($dateString);
    return date('Y-m-d',$time);
  }
}

?>
