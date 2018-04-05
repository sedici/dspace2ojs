<?php
/**
*  Default mappings for Dspace CSV export, using SEDICI repository as testing subject
*
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

class CsvMappings {
  const MAP = array(
    "LANGUAGE"=>"dc.language",
    "ISSUED_DATE"=>"dc.date.issued",
    "TITLE"=>"dc.title",
    "ABSTRACT"=>"dc.description.abstract",
    "URI"=>"dc.identifier.uri",
    "PAGES"=>"dc.format.extent",
    "ISSUE"=>"sedici.relation.journalVolumeAndIssue",
    "FULLTEXT"=>"sedici.description.fulltext",
    "KEYWORDS"=>"sedici.subject.other",
    "PERSON"=>"sedici.creator.person",
    "INSTITUTIONAL_AUTHOR" => "sedici.creator.corporate",
    "HTML_URI_TAG_XPATH_SELECTOR" => "//meta[@name='citation_pdf_url']",   //USED TO DETECT FULLTEXT URI
    "HTML_URI_ATTRIBUTE" => "content"  //ATTRIBUTE FROM TAG SELECTOR CONTAINING THE ACTUAL FULLTEXT URI
  );

  public static function get($key) {
    return isset( self::MAP[$key] ) ? self::MAP[$key] : '';
  }

}

?>
