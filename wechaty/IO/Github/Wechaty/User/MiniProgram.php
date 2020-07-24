<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 9:41 PM
 */
namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Puppet\Schemas\MiniProgramPayload;

class MiniProgram {
    public MiniProgramPayload $_payload;

    public function __construct(MiniProgramPayload $payload) {
        $this->_payload = $payload;
    }

    function getPayload() : MiniProgramPayload {
        return $this->_payload;
    }

    function appId() : ?String {
        return $this->_payload->appId;
    }

    function titile() : ?String {
        return $this->_payload->title;
    }

    function pagePath() : ?String {
        return $this->_payload->pagePath;
    }

    function username() : ?String {
        return $this->_payload->username;
    }

    function description() : ?String {
        return $this->_payload->description;
    }

    function thumbUrl() : ?String {
        return $this->_payload->thumbUrl;
    }

    function thumbKey() : ?String {
        return $this->_payload->thumbKey;
    }

    static function create() : MiniProgram {
        $payload = new MiniProgramPayload();
        return new MiniProgram($payload);
    }
}
