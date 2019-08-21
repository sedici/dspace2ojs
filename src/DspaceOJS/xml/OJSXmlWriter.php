<?php
/**
* Generates OJS3 navite import XML output from a CSV file exported by DSpace
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
namespace App\DspaceOJS\xml;
use App\DspaceOJS\helpers\Utils;
use App\Entity\File;
//include_once "src/helpers/Utils.class.php";

class OJSXmlWriter {

  private $document;
  private $articles;
  private $article_count;
  private $issue_array;
  private $issue_document;
  private $issues;

  private $default_section;
  private $default_authors_group;

  public function __construct($destination_section,$group_authors_by,$csvParser) {
    $this->article_count = 0;
    $this->issue_array = array();
    $this->default_section = $destination_section;
    $this->default_authors_group = $group_authors_by;

    $this->document = new \DOMDocument( "1.0", "UTF-8" );
    $this->articles = $this->createElement("articles", array(
      "xmlns"=>"http://pkp.sfu.ca",
      "xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance",
      "xsi:schemaLocation"=>"http://pkp.sfu.ca native.xsd"
    ));
    $this->issues = $this->createElement("issue", array(
        "xmlns"=>"http://pkp.sfu.ca",
        "xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance",
        "xsi:schemaLocation"=>"http://pkp.sfu.ca native.xsd",'published'=>'1', 
        'current'=>'0', 'access_status'=>1
      ),null,$this->document);
    $this->document->appendChild($this->issues);
    $this->addIssue($csvParser);
    $this->addDatePublishedAndLastModified($csvParser->getDateIssued());
    $this->addSections();
    $this->issues->appendChild($this->articles); 
  }

 /**
 * Ends the XML document. If $filename is provided, saves the result in the there. Otherwise, returns the XML string
 */
  private function getXMLfromDocument($filename=null,$document) {
    $document->formatOutput = true;
    if ($filename)
      return $document->save($filename);
    else
      return $document->saveXML();
  }

  public function getXML($filename=null) {
    return $this->getXMLfromDocument($filename,$this->document);
  }

  public function getIssuesXML($filename=null) {
    return $this->getXMLfromDocument($filename,$this->issue_document);
  }

 private function createElement($tag,$attributes_array, $value=null,$document = null) {
   if (!$document)
     $document = $this->document;

   $element = $document->createElement($tag,htmlspecialchars($value));
   
   foreach ($attributes_array as $key => $value)
     $element->setAttribute($key,$value);
   return $element;
 }

  /**
  * Transforms a parsed CSV record into an XML node
  */
  public function csv2xmlArticle($csvParser) {
    $this->article_count++;
    $attributes= array(
      "xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance",
      'locale' => Utils::getLocale($csvParser->getLanguage()),
      'seq'=> '1',
      'access_status' => '0',
      'date_published' => Utils::safeDate ($csvParser->getDateIssued()),
      'stage'=>"production",
      'section_ref'=>$this->default_section );
    $article = $this->createElement('article', $attributes
    );
    $this->articles->appendChild( $article );
    $this->addLocalizedMetadata($article,'title',$csvParser->getLocalizedTitle());
    $this->addLocalizedMetadata($article,'abstract',$csvParser->getLocalizedAbstract());
    $this->addKeywords($article,$csvParser->getLocalizedKeywords());
    $this->addAuthors($article, $csvParser->getAuthors());
    $this->addSubmissionFile($article,$csvParser);
    $this->addPages($article,$csvParser->getLocalizedPages());
  }

  private function addLocalizedMetadata($article,$metadata,$localizedMetadataValues) {
    foreach ($localizedMetadataValues as $key => $value) {
      $article->appendChild( $this->createElement($metadata, array('locale'=>Utils::getLocale($key) ),$value) );
    }
    return $article;
  }
  private function addDatePublishedAndLastModified($date){
    $date= Utils::safeDate($date);
    $datePublised = $this->createElement("date_published",array(),$date);
    $lastModified = $this->createElement("last_modified",array(),$date);
    
    $this->issues->appendChild($datePublised);
    $this->issues->appendChild($lastModified);

  }
  private function addSections(){
    $sections= $this->createElement("sections",array());
    $section = $this->createElement("section",array('ref'=>$this->default_section, 'seq'=>"1", 'editor_restricted'=>"1", 'meta_indexed'=>"1", 'meta_reviewed'=>"0", 'abstracts_not_required'=>"0", 'hide_title'=>"1", 'hide_author'=>"0", 'abstract_word_count'=>"0"));
    $abbrev= $this->createElement("abbrev",array('locale'=>"es_ES"),$this->default_section);
    $title= $this->createElement("title",array('locale'=>"es_ES"),$this->default_section);
    $section->appendChild($abbrev);
    $section->appendChild($title);    
    $sections->appendChild($section);
    $this->issues->appendChild($sections);
  }
  private function addPages($article,$localizedPages) {
      $pages = isset($localizedPages[Utils::$default_lang])?$localizedPages[Utils::$default_lang]: (isset($localizedPages['en']) ? $localizedPages['en']: "" );
      $article->appendChild( $this->createElement('pages',array(),$pages));
  }

  private function issueExists($issue) {
    return in_array($issue,$this->issue_array);
  }
  private function addIssue($csvParser) {
    $issue = $csvParser->getLocalizedIssue()[Utils::$default_lang];
    if ($this->issueExists($issue))
      return;

    $this->issue_array[] = $issue; //new issue registered
    $date=Utils::safeDate($csvParser->getDateIssued());
    $issue_id = $this->createElement('issue_identification',array(),null,$this->document);
    $partsDate = explode('-',(Utils::safeDate($csvParser->getDateIssued())));
    //$issue_id->appendChild( $this->createElement('volume',array(),$issue,$this->issue_document) ); //FIXME should parse $issue data
    $issue_id->appendChild( $this->createElement('number',array(),$issue,$this->document) );
    $issue_id->appendChild( $this->createElement('year',array(),$partsDate[0],$this->document) );
    $issue_id->appendChild( $this->createElement('title',array(),$issue,$this->document) );

    //$issue_node = $this->createElement('issue',array('published'=>'1', 'current'=>'0', 'access_status'=>1),null,$this->issue_document);
    $this->issues->appendChild($issue_id);
    //$this->issues->appendChild($issue_node);
  }

  private function addKeywords($article,$localizedKeywords) {
    foreach ($localizedKeywords as $lang => $keywords_array) {
      $keywords_node = $this->createElement('keywords', array('locale'=>Utils::getLocale($lang) ));
      foreach ($keywords_array as $keyword)
        $keywords_node->appendChild ( $this->createElement('keyword',array(),$keyword) );

      $article->appendChild ($keywords_node);
    }
  }
  private function addAuthors($article,$authors_array) {
    $authors_node = $this->createElement('authors', array(
      'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
      'xsi:schemaLocation'=>"http://pkp.sfu.ca native.xsd")
    );

    $authors = $authors_array['es']; //FIXME should retrieve and join authors from all languages
    if (is_string($authors)) { //institutional author
      $author_node = $this->createElement('author', array(
        "primary_contact"=>"false",
        "user_group_ref"=>$this->default_authors_group)
      );
      $author_node->appendChild( $this->createElement("givenname",array(),$authors));
      $author_node->appendChild( $this->createElement("familyname",array()," "));
      $author_node->appendChild( $this->createElement("email",array(),"mail@fake.com"));
      $authors_node->appendChild($author_node);
    } else { //regular list of people

      foreach ($authors as $author_data) {
          $author_node = $this->createElement('author', array(
            "primary_contact"=>"true",
            "user_group_ref"=>$this->default_authors_group)
          );
          foreach ($author_data as $key => $value)
            $author_node->appendChild( $this->createElement($key,array(),$value));

         $authors_node->appendChild($author_node);
      }
    }
    $article->appendChild($authors_node);
  }


  public function addSubmissionFile($article,$csvParser, $includeGalley=false) {

    if (!$csvParser->hasFulltext())
      return; //no file to be added

    $locale=$csvParser->getLanguage();
    $filedata = $csvParser->retrieveFulltext();
    if ($filedata)
      $filename='article.pdf'; //FIXME generate a better filename based on some info from the article (first author maybe?)

    $submission = $this->createElement('submission_file', array(
      "xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance",
      "xsi:schemaLocation"=>"http://pkp.sfu.ca native.xsd",
      "stage"=>"production_ready",
      "id"=>$this->article_count
    ));
   $filesize = ($filedata) ? strlen($filedata) : 0;

    $revision = $this->createElement('revision', array(
      'number'=>$this->article_count,
        'viewable' => 'true',
      'filetype'=>"application/pdf",
      //'user_group_ref'=>$this->default_authors_group,  //===> the attribute user_group_ref is not allowed in revisions
      'filename' => $filename,
      'filesize' => $filesize,
      'uploader' => 'admin'
    ));

    $revision->appendChild( $this->createElement('name', array(
                                                      'locale'=>Utils::getLocale($locale),
                                                    ), 'PDF') );
    $revision->appendChild( $this->createElement('embed', array('encoding'=>'base64'), base64_encode($filedata) ));

    $submission->appendChild( $revision );
    $article->appendChild( $submission );

   if ($includeGalley) {
      $galley = $this->createElement('article_galley', array(
        "xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance",
        "xsi:schemaLocation"=>"http://pkp.sfu.ca native.xsd",
        "approved"=>"true"));
      $galley->appendChild($this->createElement('name',array('locale'=>Utils::getLocale($locale)),'PDF'));

      $galley->appendChild($this->createElement('seq',array(),'1'));
      $galley->appendChild($this->createElement('submission_file_ref',array()));

      $article->appendChild($galley);
    }
  }
}


?>
