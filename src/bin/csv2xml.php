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

$filename = (isset($argv[1])) ? $argv[1] : FALSE;
if (!$filename) {
  echo "          Missing csv file. Example: \r\n";
  echo "\r\n              php csv2xml.php ~/journal_xyz \r\n";
  echo "\r\n \r\n          Will process ~/journal_xyz.csv file and output ~/journal_xyz.xml file \r\n \r\n \r\n";
  return;
}

$reader = new CsvReader();
$reader->open_file($filename.".csv");
$current = 0;
echo "---------Let's start-------\r\n";
$xml = new OJSXmlWriter();

while ( $record = $reader->next_record() ){

  $parser = new CsvRecordParser($record);
  $current++;


  $xml->csv2xmlArticle($parser);

}
$size= $xml->getXML($filename.'.xml');
$issues_size = $xml->getIssuesXML($filename.'_issues.xml');

echo "\r\n---------Ended with $size bytes (+ $issues_size bytes)--------\r\n";

?>
