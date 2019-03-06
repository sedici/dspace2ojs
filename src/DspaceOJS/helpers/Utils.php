<?php
/*
* @author gonetil
* @url http://sedici.unlp.edu.ar
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*/

namespace App\DspaceOJS\helpers;
class Utils {
  private static $locales = array('es'=>'es_ES', 'en'=>'en_US', 'pt'=>'pt_BR');
  public static $default_lang = 'es';

  public static function getLocale($lang) {
     return (isset(self::$locales[$lang])) ? self::$locales[$lang] : self::$locales[self::$default_lang]; }
  
  public function validateDate($date, $format = 'Y-m-d H:i:s')
  {
      $d = \DateTime::createFromFormat($format, $date);
      return $d && $d->format($format) == $date;
  }
     
  public static function safeDate($dateString) {
    $dateString.= (self::validateDate($dateString,'Y') ) ? '-01-01' : '';
    $time = strtotime($dateString);
    return date('Y-m-d',$time);
  }
}

?>
