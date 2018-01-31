<?php

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
    echo "Fetching from $uri \n";
    return file_get_contents($uri);
  }


}

 ?>
