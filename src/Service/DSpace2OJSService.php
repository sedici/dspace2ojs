<?php

namespace App\Service;
use App\DspaceOJS\csv\CsvReader;
use App\DspaceOJS\csv\CsvRecordParser;
use App\DspaceOJS\xml\OJSXmlWriter;
use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class DSpace2OJSService
{
    private $token_storage;
    private $em;

    public function __construct( TokenStorage $token_storage,EntityManagerInterface $em )
    {
        $this->token_storage = $token_storage;
        $this->em = $em;
    }
    
    public function splitFileIntoMultipleCSV($fileDir,$filename)
    {
        $aggregated_csv = fopen($fileDir.$filename . ".csv", "r");  //csv containing many collections
        $directory = dirname($fileDir.$filename . ".csv");
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
                $file= new File($this->token_storage->getToken()->getUser(),$directory . "/" . $current_collection);
                $file->setDateCreated(new \DateTime('now'));
                $file->setParentFile($filename);
                $this->em->persist($file);
                //FIXME cuando arregle el error en la app del convertior va para processfiles
                
            }
            fputcsv($csv_file, $record);
        }
        fclose($csv_file);
        $this->em->flush();
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
        $files_dir = explode('src',dirname(__FILE__))[0].'public/';
        $reader = new CsvReader();
        $reader->open_file($files_dir.$filename); 
        $current = 0;
        $xml = new OJSXmlWriter($section, $authors, new CsvRecordParser($reader->first_element()));
        while (($record = $reader->next_record()) && ($current != $max_limit)) {
            $parser = new CsvRecordParser($record);
            $current++;
            $xml->csv2xmlArticle($parser);
        }
        $filename.='.xml';
        $size = $xml->getXML($files_dir.$filename );
        return $filename;
    }
}
