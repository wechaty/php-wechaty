<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 9:48 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

use IO\Github\Wechaty\Puppet\Util\JsonUtil;

class UrlLinkPayload extends AbstractPayload {
    public $title;
    public $url;
    public $description;
    public $thumbnailUrl;

    public static $COLUMNS = array(
        "title",
        "url",
        "description",
        "thumbnailUrl",
    );

    public function __construct(String $title, String $url, $id = "") {
        $this->title = $title;
        $this->url = $url;
        parent::__construct($id);
    }

    function toJsonString() : String {
        $data = array();
        foreach(self::$COLUMNS as $value) {
            $data[$value] = $this->$value;
        }

        return JsonUtil::write($data);
    }

    public function __toString() {
        return "UrlLinkPayload(title='$this->title', url='$this->url', description=$this->description, thumbnailUrl=$this->thumbnailUrl)";
    }
}
