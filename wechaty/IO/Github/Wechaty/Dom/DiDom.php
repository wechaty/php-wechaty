<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 4:15 PM
 */
namespace IO\Github\Wechaty\Dom;

use DiDom\Document;

class DiDom extends Dom {
    public function __construct() {
        $this->_dom_parser = new Document();
    }

    public function getShareInfo($url) {
        $this->_dom_parser->loadHtmlFile($url);

        $title = "";
        $description = "";
        $image = "";

        return array(
            "title" => $title,
            "description" => $description,
            "image" => $image,
        );
    }
}