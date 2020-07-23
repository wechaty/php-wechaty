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

        $descriptionDom = $this->_dom_parser->first('head')->find('meta[name="description"]');
        $imageDom = $this->_dom_parser->find('img');

        $title = $this->_dom_parser->first('head')->first('title')->text(); // title
        $description = ""; //meta description
        $image = "";

        if(count($imageDom) > 0) {
            print_r($imageDom[0]);
            $image = $imageDom[0]->getAttribute("src");
        }
        if(count($descriptionDom) > 0) {
            $description = $descriptionDom[0]->getAttribute("content");
        }

        return array(
            "title" => $title,
            "description" => $description,
            "image" => $image,
        );
    }
}