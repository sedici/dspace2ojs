<?php

namespace App\Service;
use App\Service\csv\CsvReader;
use App\Service\csv\CsvRecordParser;
use App\Service\xml\OJSXmlWriter;

class DSpace2OJSService
{
    // private $reader;
    // public function __construct(CsvReader $reader)
    // {
    //     $this->reader = $reader;
    // }

    public function splitFileIntoMultipleCSV($filename)
    {
        $aggregated_csv = fopen($filename . ".csv", "r");  //csv containing many collections
        $directory = dirname($filename . ".csv");
        $header = fgetcsv($aggregated_csv);  //header must be replicated in each csv file
        $current_collection = null;
        $csv_file = null;
        $files = array();  //array of files to return
        while ($record = fgetcsv($aggregated_csv)) {
            $collection_safe = $this->getSingleCollection(str_replace('/', '_', $record[1]));
            $collection_safe .= '.csv';
            if ($current_collection != $collection_safe)  //new file
            {
                if ($current_collection != null)
                fclose($csv_file);

                $current_collection = $collection_safe;  //current collection has changed

                $files[] = $directory . "/" . $current_collection;  //a new file has been added to the set
                $csv_file = fopen($directory . "/" . $current_collection, 'w');
                fputcsv($csv_file, $header);
            }
            fputcsv($csv_file, $record);
        }
        fclose($csv_file);
        return $files;
    }


    /** 
      * In case an item belogs to more than one collectoin, Dspace exports it as colA || colB || colC . We are only interested in colA.
      * This function receives the string with all collections, and returns the first one of the list
      **/
    public function getSingleCollection($collection_data, $collection_separator = "|")
    {
        $collections = explode($collection_separator, $collection_data);
        return $collections[0];
    }
    public function processFiles($files, $options){
        foreach ($files as $file) {
            $this->processFile($file,$options['into_section'],$options['authors_group'],$options['limit']);
          }

    }
    /** reads s csv file and generates the XML to import into OJS */
    public function processFile($filename, $section, $authors, $max_limit )
    {
        $reader = new CsvReader();
        // var_dump($reader);die;
        $reader->open_file($filename); 
        
        $current = 0;
        $xml = new OJSXmlWriter($section, $authors, new CsvRecordParser($reader->first_element()));
        while (($record = $reader->next_record()) && ($current != $max_limit)) {
            $parser = new CsvRecordParser($record);
            $current++;
            $xml->csv2xmlArticle($parser);
        }

        $size = $xml->getXML($filename . '.xml');
        // $issues_size = $xml->getIssuesXML($filename.'_issues.xml');
    }
}
