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

   private function custom_parse_csv() {
     $handler = fopen($this->file,"r");
     if ($handler == FALSE) return;

     $result = array();
     $header = fgetcsv($handler);
     $header_count = count($header);
     $row = 1;
     while (($record = fgetcsv($handler)) !== FALSE) {
       if (count($record) != $header_count)
        echo "registro $row tiene menos campos!!! (tiene ".count($record)." vs $header_count del header)";
      else {
        $combined_array = array();
        for($i=0;$i<$header_count;$i++)
          $combined_array[$header[$i]] = $record[$i];
          //$combined_array[$header[$i]] = (empty($record[$i])) ? "" : $record[$i];
        $result[] = $combined_array;
      }
       $row++;
     }
     return $result;

   }

    private function parse_csv() {
      return $this->custom_parse_csv();
      $csv = array_map('str_getcsv', file($this->file));
      array_walk($csv, function(&$a) use ($csv) {
        $b = @array_combine($csv[0], $a);
        if ($b === FALSE)
        {
          echo "CVS PARSE ERROR: Both lines should have an equal number of elements";

          echo "CSV[0] values:"; print_r($csv[0]);
          echo "\n.......\n";
          echo "Array value"; print_r($a);
          echo "**********************************************************";
        }
        $a = $b;
      });
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
