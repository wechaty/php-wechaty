<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 9:40 PM
 */
namespace IO\Github\Wechaty\User;

use IO\Github\Wechaty\Puppet\Schemas\UrlLinkPayload;

class UrlLink {
    public UrlLinkPayload $_payload;

    public function __construct() {
        $this->_payload = new UrlLinkPayload();
    }

    function getPayload() : UrlLinkPayload {
        return $this->_payload;
    }
}