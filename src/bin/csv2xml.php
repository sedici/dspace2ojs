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
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*/

include_once "src/csv/CsvReader.class.php";
include_once "src/csv/CsvRecordParser.class.php";
include_once "src/xml/OJSXmlWriter.class.php";

$optionalParams = array('authors_group'=>'Author','into_section'=>'IMPORTED','limit'=>-1, 'split_csv'=>'no' );

$filename = (isset($argv[1])) ? $argv[1] : FALSE;
if (!$filename) {
  echo "      Missing csv file. Example: \r\n";
  echo "\r              php csv2xml.php [filename] [optional params]\r\n";
  echo "\r\n              Optional params:
        \r                - authors_group : name used for the author_group_ref (default authors)
        \r                - into_section : section to map imported articles (default IMPORTED)
        \r                - split_csv [yes | no]: if the input CSV file contains items from many collections, items can be splitted into several (smaller) csv files, one per collection
        \r                - limit : maximum number of articles to process (usefull for testing purposes)";
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
      $collection_safe = getSingleCollection( str_replace( '/','_',$record[1]) );
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


/** 
In case an item belogs to more than one collectoin, Dspace exports it as colA || colB || colC . We are only interested in colA.
This function receives the string with all collections, and returns the first one of the list

**/
function getSingleCollection($collection_data, $collection_separator="|") {
	$collections = explode($collection_separator,$collection_data);
	return $collections[0];
}

?>
