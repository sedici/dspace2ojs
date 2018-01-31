<?php
/**
*  Default mappings for OAI-PMH Driver schema, using SEDICI repository as testing subject
*
* @author gonetil
* @url http://sedici.unlp.edu.ar
* Licence GPLv3

*/
class OAIMappings {
  const MAP = array(
    "LANGUAGE"=>"dc:language",
    "ISSUED_DATE"=>"dc:date",
    "TITLE"=>"dc:title",
    "ABSTRACT"=>"dc:description",
    "URI"=>"dc:identifier",
    "PAGES"=>"dc:format",
    "ISSUE"=>"sedici.relation.journalVolumeAndIssue",
    "FULLTEXT"=>"sedici.description.fulltext",
    "KEYWORDS"=>"dc:subject",
    "PERSON"=>"dc:creator",
    "HTML_URI_TAG_XPATH_SELECTOR" => "//meta[@name='citation_pdf_url']",   //USED TO DETECT FULLTEXT URI
    "HTML_URI_ATTRIBUTE" => "content"  //ATRIBUTE FROM TAG SELECTOR CONTAINING THE ACTUAL FULLTEXT URI
  );

  public static function get($key) {
    return isset( self::MAP[$key] ) ? self::MAP[$key] : '';
  }

}

?>
