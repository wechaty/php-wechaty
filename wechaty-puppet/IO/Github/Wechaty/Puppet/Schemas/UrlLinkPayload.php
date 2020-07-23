<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 9:48 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class UrlLinkPayload extends AbstractPayload {
    public $title;
    public $url;
    public $description;
    public $thumbnailUrl;

    public function __toString() {
        return "UrlLinkPayload(title='$this->title', url='$this->url', description=$this->description, thumbnailUrl=$this->thumbnailUrl)";
    }
}