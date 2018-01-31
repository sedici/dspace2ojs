<?php

include_once "src/helpers/ErrorHandler.class.php";

class OAIXmlReader {

    private $file="";
    private $data = array();
    private $current=-1;

    public function open_file($path) {

      if (!file_exists($path))
        return ErrorHandler::error("File $path does not exist");

      if (!is_readable($path))
          return ErrorHandler::error("File $path cannot be read");

      $this->file = $path;
      $this->data = simplexml_load_file($path);//, NULL, LIBXML_NOCDATA);
      $this->reset(false);

      $count = 0;
      foreach ($this->data as $data) {
        var_dump($data->{$count++});
        if ($count ==2) die;
        }
    }

    public function open_from_url($url) {
      $document = file_get_contents($url);
      if ($document === FALSE)
        return ErrorHandler::error("Failed when fetching XML from URL $url");

      $this->file = $document;
      $this->reset(true);


    }

    private function parse_xml() {
      $document = new SimpleXMLElement($this->file);
      return $document;
    }

    public function reset($read_again=false) {
      if ($read_again !== false) {
        $this->data = $this->parse_xml();
      }
      $this->current = 0;
    }

    public function next_record() {
      echo $this->current. "=====>".$this->data->ListRecords->record[$this->current]->metadata->{'oai_dc:dc'}->{'dc:title'};
      echo "\n";
      if ( $this->has_records() )
        return $this->data->ListRecords[$this->current++];
      else
        return false;
    }

    public function has_records() {
      return (( count($this->data->ListRecords) > 0) && (count($this->data->ListRecords) > $this->current) ); }

}

?>
