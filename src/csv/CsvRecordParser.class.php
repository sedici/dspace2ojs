<?php

/**
* Parses metadata from one single record extracted from a CSV file
* Currently supports only SEDICI UNLP metadata schema.
*
* @author gonetil
* @url http://sedici.unlp.edu.ar
* Licence GPLv3
*
*/

  include_once 'src/xml/HtmlParser.class.php';
  include_once 'src/helpers/Mappings.class.php';

class CsvRecordParser {

      private $languages = array("es","en","pt");
      private $default_lang = 'es';

      private $data = array();

      public function __construct($record) {
          $this->data = $record;
      }

      private function getValue($key, $lang=null) {
        $key = Mappings::get($key) . ($lang ? "[$lang]" : ""); //fetches the real key
        return (isset($this->data[$key])) ? $this->data[$key] : false;
      }

      private function getLocalizedMetadata($key) {
        $metadata = array();
        foreach ($this->languages as $lang) {
            if ($datum = $this->getValue($key,$lang))
              $metadata[$lang] = $datum;
        }
        return $metadata;
      }

      /**
        @param $value  string of values to be splitted
        @param $callback function to apply to each value after it was splitted
        @param $delimiter  string used to split $value into multiple values
      */
      private function splitMultivariate($value,$callback = null, $delimiter="||") {
        $exploded = explode($delimiter,$value);
        return ($callback) ? array_map($callback,$exploded) : $exploded;
      }

      private function splitLocalizedMultivariate($record_array, $callback=null) {
        foreach ($record_array as $key => $value) {
          $record_array[$key] = $this->splitMultivariate($value,$callback);
        }
        return $record_array;

      }

      /**
      * Generates a fake email address for each author like firstname@lastname.fake
      */
      private function getEmail($splittedAuthor) {
        $invalid_characters = array("$", "%", "#", "<", ">", "|", " ", "'", "`",".",",");
        return str_replace($invalid_characters, "", $splittedAuthor[1]).'@'.str_replace($invalid_characters, "", $splittedAuthor[0]) . '.fake';
        }

      private function authorStringToArray($string) {
        $split = explode(",",$string);
        $author = array( "firstname" => $split[1],
                         "lastname" => $split[0],
                         "email" => $this->getEmail($split)
                        );
        return $author;
      }

      /**
      * Returns the value of dc.language metadata if exists, or the default_lang otherwise
      */
      public function getLanguage()
      {
        $lang = $this->getLocalizedMetadata("LANGUAGE");
        return (isset($lang[$this->default_lang])) ? $lang[$this->default_lang] : $this->default_lang;
      }

      public function getDateIssued() { return $this->getValue("ISSUED_DATE"); }
      public function getLocalizedTitle() { return $this->getLocalizedMetadata("TITLE");  }
      public function getLocalizedAbstract() { return $this->getLocalizedMetadata("ABSTRACT"); }
      public function getUri() { return $this->getValue('URI'); }
      public function getLocalizedPages() { return $this->getLocalizedMetadata('PAGES'); }
      public function getLocalizedIssue() { return $this->getLocalizedMetadata('ISSUE'); }
      public function hasFulltext() {
        $fulltextMetadata = $this->getLocalizedMetadata('FULLTEXT');
        return ($fulltextMetadata[$this->default_lang] == 'true');
      }

      public function getFulltextUri() {
        $htmParser = new HtmlParser(
          $this->getValue('URI'),
          Mappings::get('HTML_URI_TAG_XPATH_SELECTOR'),
          Mappings::get('HTML_URI_ATTRIBUTE')
        );
        return $htmParser->getFileUri();
      }

      public function retrieveFulltext() {
        $uri = $this->getFulltextUri();
        $file = file_get_contents($uri);
        return $file;

      }

      public function getLocalizedKeywords($callback=null) {
        $keywords = $this->getLocalizedMetadata("KEYWORDS");
        return $this->splitLocalizedMultivariate($keywords,$callback);
      }

      public function getAuthors() {

        $authors = $this->splitLocalizedMultivariate (
            $this->getLocalizedMetadata("PERSON"),
            array($this,'authorStringToArray')
          );

        return $authors;
      }

}


?>
