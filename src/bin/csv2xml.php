<?php

/**
* Test script for CSV2XML (DSpace2OJS) parser
* Using the string $testfile, reads the contents of $testfile.csv file and outputs $testfile.xml .
* To do so, parses each records from the input CSV file, from wich generates the OJS-native XML structure.
* In addition, retrieves PDF documents from the repository webpages, which are base64 encoded and inserted into the
* generated xml document
*
*
* @author gonetil
* @url http://sedici.unlp.edu.ar
* Licence GPLv3
*
*/

include_once "src/csv/CsvReader.class.php";
include_once "src/csv/CsvRecordParser.class.php";
include_once "src/xml/OJSXmlWriter.class.php";

$optionalParams = array('authors_group'=>'authors','into_section'=>'IMPORTED','limit'=>-1, 'split_csv'=>'no' );

$filename = (isset($argv[1])) ? $argv[1] : FALSE;
if (!$filename) {
  echo "      Missing csv file. Example: \r\n";
  echo "\r              php csv2xml.php [filename] [optional params]\r\n";
  echo "\r\n              Optional params:
        \r                - authors_group : name used for the author_group_ref (default authors)
        \r                - into_section : section to map imported articles (default IMPORTED)
        \r                - split_csv [yes | no]: if the input CSV file contains items from many collections, items can be splitted into several (smaller) csv files, one per collection";
  echo "\r\n      Example:
        \r      php csv2xml.php ~/journal_xyz authors_group=autores into_section=IMPORTADOS split=no\r\n";
  echo "\r      Will process ~/journal_xyz.csv file and output ~/journal_xyz.xml file . Authors will be mapped into autores group (which must exist in OJS), and articles will be placed into IMPORTADOS section (which must also exist in OJS) \r\n \r\n \r\n";
  return;
}

//process the remaining (optional) params
for($i=2;$i<count($argv); $i++) {
	list($param_name,$param_value)=explode('=',$argv[$i]);
	if (isset($optionalParams[$param_name])) {
		$optionalParams[$param_name] = $param_value;
  }
	else
		echo "\r\nParam $param_name unknown. Ignoring..\r\n";
 }

$authors = $optionalParams['authors_group'];
$section = $optionalParams['into_section'];
$max_limit = $optionalParams['limit'];
$split = ( $optionalParams['split_csv'] == 'yes' );


if ($split)
  $files = splitFileIntoMultipleCSV($filename);
else
  $files = array( $filename.'.csv');  //there is only one file to process

foreach ($files as $file) {
  processFile($file,$section,$authors,$max_limit);
}

/** reads s csv file and generates the XML to import into OJS */
function processFile($filename,$section,$authors,$max_limit) {
    $reader = new CsvReader();
    $reader->open_file($filename);

    $current = 0;
    echo "FILE $filename : Let's start-------\r\n";
    $xml = new OJSXmlWriter($section,$authors);

    while ( ($record = $reader->next_record() ) && ( $current != $max_limit ) )
    {
      $parser = new CsvRecordParser($record);
      $current++;
      $xml->csv2xmlArticle($parser);
    }

    $size= $xml->getXML($filename.'.xml');
    $issues_size = $xml->getIssuesXML($filename.'_issues.xml');

    echo "\r\n---------Ended with $size bytes (+ $issues_size bytes)--------\r\n";
}

function splitFileIntoMultipleCSV($filename) {
  echo "Splitting input file \r ************ \r";

  $aggregated_csv = fopen($filename.".csv","r");  //csv containing many collections
  $directory = dirname($filename.".csv");
  $header = fgetcsv($aggregated_csv);  //header must be replicated in each csv file


  $current_collection = null;
  $csv_file = null;

  $files = array();  //array of files to return
  while ( $record = fgetcsv($aggregated_csv) ) {
      $collection_safe = str_replace( '/','_',$record[1]);
      $collection_safe .= '.csv';
      if ($current_collection != $collection_safe)  //new file
      {
         if ($current_collection!=null)
            fclose($csv_file);

          $current_collection = $collection_safe;  //current collection has changed

          $files[] = $directory."/".$current_collection;  //a new file has been added to the set
          $csv_file = fopen($directory."/".$current_collection,'w');
          fputcsv($csv_file,$header);
      }
      fputcsv($csv_file,$record);
  }

  fclose($csv_file);

  return $files;
}
?>
