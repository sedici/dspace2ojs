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

$optionalParams = array('authors_group'=>'authors','into_section'=>'IMPORTED','limit'=>-1 );

$filename = (isset($argv[1])) ? $argv[1] : FALSE;
if (!$filename) {
  echo "      Missing csv file. Example: \r\n";
  echo "\r              php csv2xml.php [filename] [optional params]\r\n";
  echo "\r\n              Optional params:
        \r                - authors_groups : name used for the author_group_ref (default authors)
        \r                - into_section : section to map imported articles (default IMPORTED)";
  echo "\r\n      Example:
        \r      php csv2xml.php ~/journal_xyz authors_group=autores into_section=IMPORTADOS\r\n";
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

$reader = new CsvReader();
$reader->open_file($filename.".csv");
$current = 0;
echo "---------Let's start-------\r\n";
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

?>
