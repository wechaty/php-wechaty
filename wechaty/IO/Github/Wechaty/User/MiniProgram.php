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

    public function __construct() {
        $this->_payload = new MiniProgramPayload();
    }

    function getPayload() : MiniProgramPayload {
        return $this->_payload;
    }

    function appid(): ?string{
        return $this->_payload->appid;
    }

    function title(): ?string{
        return $this->_payload->title;
    }

    function pagePath(): ?string{
        return $this->_payload->pagePath;
    }

    function username(): ?string{
        return $this->_payload->username;
    }

    function description(): ?string{
        return $this->_payload->description;
    }

    function thumbUrl(): ?string{
        return $this->_payload->thumbUrl;
    }

    function thumbKey(): ?string{
        return $this->_payload->thumbKey;
    }
}
