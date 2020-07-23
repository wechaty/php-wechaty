<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 9:40 PM
 */
namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Dom\DomFactory;
use IO\Github\Wechaty\Puppet\Schemas\UrlLinkPayload;

class UrlLink {
    public UrlLinkPayload $_payload;

    public function __construct(UrlLinkPayload $payload) {
        $this->_payload = $payload;
    }

    function getPayload() : UrlLinkPayload {
        return $this->_payload;
    }

    function url () : String {
        return $this->_payload->url;
    }

    function title () : String {
        return $this->_payload->title;
    }

    function thumbnailUrl () : ?String {
        return $this->_payload->thumbnailUrl;
    }

    function description () : ?String {
        return $this->_payload->description;
    }

    public function __toString() {
        return "UrlLink(payload=$this->_payload)";
    }

    static function create(String $url) : UrlLink {
        $meta = DomFactory::getDom()->getShareInfo($url);

        $imageUrl = "";

        $images = $meta["image"];
        if(stripos($images, "http") !== 0) {
            if(stripos($images, "/") !== 0) {
                $imageUrl = "$url/$images";
            } else {
                $imageUrl = $url . $images;
            }
        } else {
            $imageUrl = $images;
        }

        $title = $meta["title"];
        $description = $meta["description"];
        if(empty($description)) {
            $description = $title;
        }

        $payload = new UrlLinkPayload($title, $url);
        $payload->description = $description;
        $payload->thumbnailUrl = $imageUrl;

        return new UrlLink($payload);
    }
}