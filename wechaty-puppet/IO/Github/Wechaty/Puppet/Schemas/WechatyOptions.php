<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/10
 * Time: 7:30 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class WechatyOptions {
    public $name = "Wechaty";
    public $puppet = "\\IO\\Github\\Wechaty\\PuppetService\\PuppetService";
    public $puppetOptions = null;
    public $ioToken = null;
}