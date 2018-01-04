<?php

include_once "src/helpers/ErrorHandler.class.php";

class CsvReader {

    private $file="";
    private $data = array();
    private $current=-1;

    public function open_file($path) {
      if (!file_exists($path))
        return ErrorHandler::error("File $path does not exist");

      if (!is_readable($path))
          return ErrorHandler::error("File $path cannot be read");

      $this->file = $path;
      $this->reset(true);
    }

    private function parse_csv() {
      $csv = array_map('str_getcsv', file($this->file));
      array_walk($csv, function(&$a) use ($csv) {  $a = array_combine($csv[0], $a); });
      array_shift($csv); #remove header
      return $csv;
    }

    public function reset($read_again=false) {
      if ($read_again !== false) {
        $this->data = $this->parse_csv();
      }
      $this->current = 0;
    }

    public function next_record() {
      if ( $this->has_records() )
        return $this->data[$this->current++];
      else
        return false;
    }

    public function has_records() {

      return (( count($this->data) > 0) && (count($this->data) > $this->current) ); }

}

?>
