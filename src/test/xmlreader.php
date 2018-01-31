<?php

/**
* Test script for DSpace2OJS parser
* Using the string $testfile, reads the contents of $testfile.oai.xml file and outputs $testfile.xml .
* To do so, parses each records from the input XML file, from wich generates the OJS-native XML structure.
* In addition, retrieves PDF documents from the repository webpages, which are base64 encoded and inserted into the
* generated xml document
*
*
* @author gonetil
* @url http://sedici.unlp.edu.ar
* Licence GPLv3
*
*/

include_once "src/OAI/OAIXmlReader.class.php";
include_once "src/OAI/XmlRecordParser.class.php";
//include_once "src/xml/Csv2XmlWriter.class.php";

$testfile = "./samples/TEyET_OAI.oai";

$reader = new OAIXmlReader();
$reader->open_file($testfile.".xml");
$current = 0;
echo "---------Let's start-------\r\n";
//$xml = new Csv2XmlWriter();
while ( ($record = $reader->next_record() ) && ($current < 1000) ){


  $parser = new XmlRecordParser($record);
  $current++;
  echo "\r\n Registro $current ---- \r\n";
  print_r($parser->getLocalizedTitle());
  print_r($parser->getAuthors());
  print_r($parser->getLocalizedIssue());
  //$xml->csv2xmlArticle($parser);

}
/*
$size= $xml->getXML($testfile.'.xml');
$issues_size = $xml->getIssuesXML($testfile.'_issues.xml');

echo "\r\n---------Ended with $size bytes (+ $issues_size bytes)--------\r\n";
*/
?>
