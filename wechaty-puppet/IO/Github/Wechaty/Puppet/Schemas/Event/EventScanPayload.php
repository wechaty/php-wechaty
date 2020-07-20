<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/19
 * Time: 8:16 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas\Event;

class EventScanPayload {
    const SCAN_STATUS_UNKNOWN = -1;
    const SCAN_STATUS_CANCEL = 0;
    const SCAN_STATUS_WAITING = 1;
    const SCAN_STATUS_SCANNED = 2;
    const SCAN_STATUS_CONFIRMED = 3;
    const SCAN_STATUS_TIMEOUT = 4;

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