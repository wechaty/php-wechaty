<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/19
 * Time: 8:16 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas\Event;

class EventScanPayload {
    public $status = null;
    public $qrcode = null;
    public $data = null;

    public function __construct($json = false) {
        if($json) {
            $this->set(json_decode($json, true));
        }
    }

    public function set($data) {
        foreach($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function __toString() {
        return "EventScanPayload(status=$this->status, qrcode=$this->qrcode, data=$this->data)";
    }
}