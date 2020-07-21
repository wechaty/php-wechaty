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
}