<?php
  include_once 'src/xml/HtmlParser.class.php';
/**
Parses data from one single record extracted from a CSV file

*/

class CsvRecordParser {

      private $languages = array("es","en","pt");
      private $default_lang = 'es';

      private $data = array();

      public function __construct($record) {
          $this->data = $record;
      }

      private function getValue($key) {
        if (isset($this->data[$key]))
          return $this->data[$key];
        else
          return false;
      }

      private function getLocalizedMetadata($key) {
        $metadata = array();
        foreach ($this->languages as $lang) {
            if ($datum = $this->getValue($key."[".$lang."]"))
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
        $lang = $this->getLocalizedMetadata("dc.language");
        return (isset($lang[$this->default_lang])) ? $lang[$this->default_lang] : $this->default_lang;
      }

      public function getDateIssued() { return $this->getValue("dc.date.issued"); }
      public function getLocalizedTitle() { return $this->getLocalizedMetadata("dc.title");  }
      public function getLocalizedAbstract() { return $this->getLocalizedMetadata("dc.description.abstract"); }
      public function getUri() { return $this->getValue('dc.identifier.uri'); }
      public function getLocalizedPages() { return $this->getLocalizedMetadata('dc.format.extent'); }
      public function getLocalizedIssue() { return $this->getLocalizedMetadata('sedici.relation.journalVolumeAndIssue'); }
      public function hasFulltext() {
        $fulltextMetadata = $this->getLocalizedMetadata('sedici.description.fulltext');
        return ($fulltextMetadata[$this->default_lang] == 'true');
      }

      public function getFulltextUri() {
        $htmParser = new HtmlParser($this->getValue('dc.identifier.uri'));
        return $htmParser->getFileUri();
      }

      public function retrieveFulltext() {
        $uri = $this->getFulltextUri();
        $file = file_get_contents($uri);
        return $file;

      }

      public function getLocalizedKeywords($callback=null) {
        $keywords = $this->getLocalizedMetadata("sedici.subject.other");
        return $this->splitLocalizedMultivariate($keywords,$callback);
      }

      public function getAuthors() {

        $authors = $this->splitLocalizedMultivariate (
            $this->getLocalizedMetadata("sedici.creator.person"),
            array($this,'authorStringToArray')
          );

        return $authors;
      }

}


?>
