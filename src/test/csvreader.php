<?php

/**
* Test script for CSV2XML (DSpace2OJS) parser
* Using the string $testfile, reads the contents of $testfile.csv file and outputs $testfile.xml .
* To do so, parses each records from the input CSV file, from wich generates the OJS-native XML structure.
* In addition, retrieves PDF documents from the repository webpages, which are base64 encoded and inserted into the
* generated xml document
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

$testfile = "./samples/10915-836";

$reader = new CsvReader();
$reader->open_file($testfile.".csv");
$current = 0;
echo "---------Let's start-------\r\n";
$xml = new OJSXmlWriter();
while ( ($record = $reader->next_record() ) && ($current < 20) ){

  $parser = new CsvRecordParser($record);
  $current++;

  echo "\r\n Registro $current \r\n";
  $title = $parser->getLocalizedTitle();
  if (isset($title['es'])) echo $title['es'];
  else if (isset($title['en'])) echo $title['en'];
  else echo $title['pt'];
  echo "\r\n----------------------------\r\n";

  $xml->csv2xmlArticle($parser);

}
$size= $xml->getXML($testfile.'.xml');
$issues_size = $xml->getIssuesXML($testfile.'_issues.xml');

echo "\r\n---------Ended with $size bytes (+ $issues_size bytes)--------\r\n";

?>
