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
namespace App\Service\AbstractClass;
abstract class RecordParser {
  protected $languages = array("es","en","pt");
  protected $default_lang = 'es';

  protected $data = array();

  public function __construct($record) {
      $this->data = $record;
  }

  public abstract function getLanguage();
  public abstract function getDateIssued();
  public abstract function getLocalizedTitle();
  public abstract function getLocalizedAbstract();
  public abstract function getUri();
  public abstract function getLocalizedPages();
  public abstract function getLocalizedIssue();
  public abstract function hasFulltext();
  public abstract function getLocalizedKeywords();
  public abstract function getAuthors();


  public abstract function getFulltextUri();

  public function retrieveFulltext() {
    $uri = $this->getFulltextUri();
    if ($uri) { 
       echo "Fetching from $uri \n";
       return file_get_contents($uri); 
      } else {
        echo "Found record with no full text available";
        return false;
      }
  }


}

 ?>
