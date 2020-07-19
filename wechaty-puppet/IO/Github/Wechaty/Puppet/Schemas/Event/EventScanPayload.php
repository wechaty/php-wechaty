<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/19
 * Time: 8:16 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas\Event;

class EventScanPayload {
    public $status;
    public $qrcode;
    public $data;

    public function __toString() {
        return "EventScanPayload(status=$this->status, qrcode=$this->qrcode, data=$this->data)";
    }
}