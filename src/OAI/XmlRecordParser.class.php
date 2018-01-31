<?php

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
