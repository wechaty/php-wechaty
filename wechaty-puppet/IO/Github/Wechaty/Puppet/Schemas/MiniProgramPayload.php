<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 9:46 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

use IO\Github\Wechaty\Puppet\Util\JsonUtil;

class MiniProgramPayload extends AbstractPayload {
    public $appid = null;
    public $description = null;
    public $pagePath = null;
    public $thumbUrl = null;
    public $title = null;
    public $username = null;
    public $thumbKey = null;

    public static $COLUMNS = array(
        "appid",
        "description",
        "pagePath",
        "thumbUrl",
        "title",
        "username",
        "thumbKey",
    );

    function toJsonString() : String {
        $data = array();
        foreach(self::$COLUMNS as $value) {
            $data[$value] = $this->$value;
        }

        return JsonUtil::write($data);
    }
}