<?php
/*
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
