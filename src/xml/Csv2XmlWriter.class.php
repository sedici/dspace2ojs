<?php

include_once "src/helpers/Utils.class.php";

class Csv2XmlWriter {

  private $document;
  private $articles;
  private $article_count;

  public function __construct() {
    $this->article_count = 0;
    $this->document = new DOMDocument( "1.0", "UTF-8" );

    $this->articles = $this->createElement("articles", array(
      "xmlns"=>"http://pkp.sfu.ca",
      "xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance",
      "xsi:schemaLocation"=>"http://pkp.sfu.ca native.xsd"
    ));
    $this->document->appendChild($this->articles);
  }

 /**
 * Ends the XML document. If $filename is provided, saves the result in the there. Otherwise, returns the XML string
 */
  public function getXML($filename=null) {
    $this->document->formatOutput = true;
    if ($filename)
      return $this->document->save($filename);
    else
      return $this->document->saveXML();
  }

 private function createElement($tag,$attributes_array, $value=null) {
   $element = $this->document->createElement($tag,$value);
   foreach ($attributes_array as $key => $value)
     $element->setAttribute($key,$value);
   return $element;
 }

  /**
  * Transforms a parsed CSV record into an XML node
  */
  public function csv2xmlArticle($csvParser) {
    $this->article_count++;
    $article = $this->createElement('article', array(
      "xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance",
      'locale' => Utils::getLocale($csvParser->getLanguage()),
      //'date_published' => Utils::safeDate ($csvParser->getDateIssued()),
      'stage'=>"production",
      'section_ref'=>'ART' )  //FIXME allow different sections
    );

    $this->addLocalizedMetadata($article,'title',$csvParser->getLocalizedTitle());
    $this->addLocalizedMetadata($article,'abstract',$csvParser->getLocalizedAbstract());
    $this->addKeywords($article,$csvParser->getLocalizedKeywords());
    $this->addAuthors($article, $csvParser->getAuthors());
    $this->addSubmissionFile($article,$csvParser);
  //  $this->addPages($article,$csvParser->getLocalizedPages());
  //  $this->addIssue($article,$csvParser->getLocalizedIssue());
    $this->articles->appendChild( $article );

  }

  private function addLocalizedMetadata($article,$metadata,$localizedMetadataValues) {
    foreach ($localizedMetadataValues as $key => $value) {
      $article->appendChild( $this->createElement($metadata, array('locale'=>Utils::getLocale($key) ),$value) );
    }
    return $article;
  }

  private function addPages($article,$localizedPages) {
      $pages = isset($localizedPages[Utils::$default_lang])?$localizedPages[Utils::$default_lang]:$localizedPages['en'];
      $article->appendChild( $this->createElement('pages',array(),$pages));
  }

  private function addIssue($article,$localizedIssue) {
    $issue = $localizedIssue[Utils::$default_lang];
    $issue_id = $this->createElement('issue_identification',array() );
    $issue_id->appendChild( $this->createElement('volume',array(),$issue) );
    $issue_id->appendChild( $this->createElement('number',array()) );
    $issue_id->appendChild( $this->createElement('year',array()) );

    $article->appendChild($issue_id);
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
    foreach ($authors as $author_data) {
        $author_node = $this->createElement('author', array(
          "primary_contact"=>"true",
          "user_group_ref"=>"Author")
        );
        foreach ($author_data as $key => $value)
          $author_node->appendChild( $this->createElement($key,array(),$value));

       $authors_node->appendChild($author_node);
    }
    $article->appendChild($authors_node);
  }


  public function addSubmissionFile($article,$csvParser, $includeGalley=false) {

    if (!$csvParser->hasFulltext())
      return; //no file to be added

    $locale=$csvParser->getLanguage();
    $filedata = $csvParser->retrieveFulltext();
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
  //    'genre' => 'SUBMISSION',
      'viewable' => 'true',
      'filetype'=>"application/pdf",
      'user_group_ref'=>"Author",
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
