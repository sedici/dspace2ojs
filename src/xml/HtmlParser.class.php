<?php

class HtmlParser {

  private $page = '';
  private $domxpath = null;
  private $xpath_selector;
  private $xpath_attribute;
    public function __construct($uri,$xpath_selector,$xpath_attribute) {
        $this->page = new DOMDocument();
        $this->page->preserveWhiteSpace = FALSE;
        @$this->page->loadHTMLFile($uri);

        $this->domxpath = new DOMXPath($this->page);
        $this->xpath_attribute = $xpath_attribute;
        $this->xpath_selector = $xpath_selector;
        return $this->page;
    }

    public function getFileUri() {
    
      return $this->domxpath->query($this->xpath_selector)->item(0)->getAttribute($this->xpath_attribute);
    }


}
