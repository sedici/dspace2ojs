<?php
/**
*  Default mappings for Dspace CSV export, using SEDICI repository as testing subject
*
* @author gonetil
* @url http://sedici.unlp.edu.ar
* Licence GPLv3

*/
class Mappings {
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
    "HTML_URI_TAG_XPATH_SELECTOR" => "//meta[@name='citation_pdf_url']",   //USED TO DETECT FULLTEXT URI
    "HTML_URI_ATTRIBUTE" => "content"  //ATRIBUTE FROM TAG SELECTOR CONTAINING THE ACTUAL FULLTEXT URI
  );

  public static function get($key) {
    return isset( self::MAP[$key] ) ? self::MAP[$key] : '';
  }

}

?>
