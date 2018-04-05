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

include_once "src/Abstract/RecordParser.class.php";
include_once "src/helpers/OAIMappings.class.php";

class XMLRecordParser extends RecordParser {

  private function getValue($metadata) {
    $key = OAIMappings::get($metadata); //fetches the real key

    echo "$metadata -> $key ................... \n";
    //var_dump($this->data->metadata);
    $values = array();
    //foreach ($this->data->metadata->{'$key'} as $val) {
    //  $values[] = $val;
    //}
    var_dump($values);
    return (count($values) > 1) ? $values : $values[0]; //returns a string if there is only one value, the whole array otherwise
  }

  public function getLanguage() {
    return $this->getValue("LANGUAGE");
  }
  public function getDateIssued() { return $this->getValue("ISSUED_DATE"); }
  public function getLocalizedTitle() { return $this->getValue("TITLE"); }
  public function getLocalizedAbstract() { return $this->getValue("ABSTRACT"); }
  public function getUri() { return $this->getValue("URI"); }
  public function getLocalizedPages() { return $this-getValue("PAGES"); }
  public function getLocalizedIssue()  { return $this->getValue("ISSUE"); } //FIXME no issue information included in OAI records
  public function hasFulltext() { return true; } //FIXME find out how to check whether there is a fulltext available
  public function getLocalizedKeywords() { return $this->getValue("KEYWORDS"); }
  public function getAuthors() { return $this->getValue("AUTHORS"); }
}
?>
