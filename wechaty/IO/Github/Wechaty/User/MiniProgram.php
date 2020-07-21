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
    public MiniProgramPayload $payload;

    public function __construct() {
        $this->payload = new MiniProgramPayload();
    }
}