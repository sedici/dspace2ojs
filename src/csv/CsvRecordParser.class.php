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

  include_once 'src/helpers/CsvMappings.class.php';
  include_once 'src/Abstract/RecordParser.class.php';
  include_once 'src/xml/HtmlParser.class.php';

class CsvRecordParser extends RecordParser {

      private function getValue($key, $lang=null) {
        $key = CsvMappings::get($key) . ( ($lang!==null) ? "[$lang]" : ""); //fetches the real key
        return (isset($this->data[$key])) ? $this->data[$key] : false;
      }

      private function getLocalizedMetadata($key) {
        $metadata = array();
        foreach ($this->languages as $lang) {
            if ($datum = $this->getValue($key,$lang))
              $metadata[$lang] = $datum;
        }

        /*
        * These lines cover special cases with no lang or an empty lang value, such as:
        * dc.date.issued    ===> no lang
        *	dc.date.issued[]	===> empty lang value
        * dc.date.issued[es]===> regular case, covered above
        */
       $datum_no_lang=$this->getValue($key);  //check if there is a record with no lang for the same key
       $datum_empty_lang=$this->getValue($key,''); //check if there is a record with an empty lang value for the same key

       if (!empty($datum_no_lang) || !empty($datum_empty_lang))
          $metadata['NO_LANG'] =  ( empty($datum_no_lang) ? $datum_empty_lang : $datum_no_lang);

        return $metadata;
      }

      /**
      * When a localized metadata has several values, including values with no locale or empty locale, and we need
      * to choose one single value (e.g. to fetch the URI), we first select the value with no or empty locale. If that's empty,
      * we go for the default locale. Otherwise, we go for anyone with a value
      */
      private function pickBestValue($localizedMetadata) {

        if (isset($localizedMetadata['NO_LANG']))
          return $localizedMetadata['NO_LANG'];
        else if (isset($localizedMetadata[$this->default_lang]))
          return $localizedMetadata[$this->default_lang];
        else
          foreach ($this->languages as $lang)
            if (isset($localizedMetadata[$lang]))
              return $localizedMetadata[$lang];
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
      public function getUri() {
        $uri = $this->getValue('URI');
        if (empty($uri))
          $uri = $this->pickBestValue($this->getLocalizedMetadata('URI'));
        return $uri;
       }
      public function getLocalizedPages() { return $this->getLocalizedMetadata('PAGES'); }
      public function getLocalizedIssue() { return $this->getLocalizedMetadata('ISSUE'); }
      public function hasFulltext() {
        $fulltextMetadata = $this->getLocalizedMetadata('FULLTEXT');
        return (isset($fulltextMetadata[$this->default_lang]) && $fulltextMetadata[$this->default_lang] == 'true');
      }

      public function getFulltextUri() {

        $htmlParser = new HtmlParser($this->getUri(),
                CsvMappings::get('HTML_URI_TAG_XPATH_SELECTOR'),
                CsvMappings::get('HTML_URI_ATTRIBUTE'));
        return $htmlParser->getFileUri();

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
