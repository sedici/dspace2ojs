<?php


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

  echo "\r\n Registro $current ---- \r\n"; $current++;
  print_r($parser->getLocalizedTitle());
  $xml->csv2xmlArticle($parser);

/*  print_r($record);
  print_r($parser->getLocalizedAbstract());
  print_r($parser->getAuthors());

  print_r($parser->getLocalizedPages());
  print_r($parser->getLocalizedIssue());

  var_dump($parser->hasFulltext());
  print_r($parser->getUri()); */
//  var_dump( base64_encode( $parser->retrieveFulltext() ) );

}
$size= $xml->getXML($testfile.'.xml');

echo "\r\n---------Ended with $size bytes--------\r\n";

?>
