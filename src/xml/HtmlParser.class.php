<?php

class HtmlParser {

  private $page = '';
  private $domxpath = null;
    public function __construct($uri) {

        $this->page = new DOMDocument();
        $this->page->preserveWhiteSpace = FALSE;
        @$this->page->loadHTMLFile($uri);

        $this->domxpath = new DOMXPath($this->page);
        return $this->page;
    }

    public function getFileUri() {
      return $this->domxpath->query("//meta[@name='citation_pdf_url']")->item(0)->getAttribute('content');
    }


}
