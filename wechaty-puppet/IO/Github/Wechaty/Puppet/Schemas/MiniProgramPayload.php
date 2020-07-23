<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 9:46 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class MiniProgramPayload extends AbstractPayload {
    public $appid = null;
    public $description = null;
    public $pagePath = null;
    public $iconUrl = null;
    public $shareId = null;
    public $thumbUrl = null;
    public $title = null;
    public $username = null;
    public $thumbKey = null;
}
