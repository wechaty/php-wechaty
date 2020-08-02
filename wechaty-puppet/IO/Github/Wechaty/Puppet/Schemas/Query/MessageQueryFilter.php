<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/8/2
 * Time: 7:39 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas\Query;

class MessageQueryFilter extends AbstractQueryFilter {
    public $fromId = null;
    public $id = null;
    public $roomId = null;
    public $text = null;
    public $toId = null;
    public $type = null;
    public $textReg = null;

    /*public function __toString() {
        return "MessageQueryFilter(fromId=$this->fromId, id=$this->id, roomId=$this->roomId, text=$this->text, toId=$this->toId, type=$this->type, textReg=$this->textReg)";
    }*/
}