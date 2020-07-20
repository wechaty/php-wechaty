<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 12:52 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas;

class ContactPayload {
    const CONTACTGENDER_UNKNOWN = 0;
    const CONTACTGENDER_MALE = 1;
    const CONTACTGENDER_FEMALE = 2;

    const CONTACTTYPE_UNKNOWN = 0;
    const CONTACTTYPE_PERSONAL = 1;
    const CONTACTTYPE_OFFICIAL = 2;

    public $id = null;
    public $gender = null;
    public $type = null;
    public $name = null;
    public $avatar = null;
    public $address = null;
    public $alias = null;
    public $city = null;
    public $friend = null;
    public $province = null;
    public $signature = null;
    public $star = null;
    public $weixin = null;

    public function __toString() {
        return "ContactPayload(id=$this->id, gender=$this->gender, type=$this->type, name=$this->name, avatar=$this->avatar, address=$this->address, alias=$this->alias, city=$this->city, friend=$this->friend, province=$this->province, signature=$this->signature, star=$this->star, weixin=$this->weixin)";
    }
}