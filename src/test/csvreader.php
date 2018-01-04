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
include_once "src/xml/Csv2XmlWriter.class.php";

$testfile = "./samples/10915-57949";

$reader = new CsvReader();
$reader->open_file($testfile.".csv");
$current = 0;
echo "---------Let's start-------\r\n";
$xml = new Csv2XmlWriter();
while ($record = $reader->next_record() ) {

  $parser = new CsvRecordParser($record);
  $current++;
  echo "\r\n Registro $current ---- \r\n";
  print_r($parser->getLocalizedTitle());
  $xml->csv2xmlArticle($parser);

}
$size= $xml->getXML($testfile.'.xml');

echo "\r\n---------Ended with $size bytes--------\r\n";

?>
