<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 3:25 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

abstract class AbstractPayload {
    public $id = null;

    public function __construct($id = null) {
        $this->id = $id;
    }
}