<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/8/2
 * Time: 7:39 PM
 */
namespace IO\Github\Wechaty\Puppet\Schemas\Query;

class MessageQueryFilter {
    public $fromId = null;
    public $id = null;
    public $roomId = null;
    public $text = null;
    public $toId = null;
    public $type = null;
    public $textReg = null;

    public function __toString() {
        return "MessageQueryFilter(fromId=$this->fromId, id=$this->id, roomId=$this->roomId, text=$this->text, toId=$this->toId, type=$this->type, textReg=$this->textReg)";
    }

    public static function getProperties() {
        $ref = new \ReflectionClass(self::class);
        $props = $ref->getProperties();
        $properties = array();
        foreach ($props as $key => $val) {
            if($val->isPublic()) {
                $properties[] = $val->getName();
            }
        }

        return $properties;
    }

    public static function reflection() {
        $ref = new \ReflectionClass("\\IO\\Github\\Wechaty\\Puppet\\Schemas\\Query\\MessageQueryFilter");

        $consts = $ref->getConstants(); //返回所有常量名和值
        echo "----------------consts:---------------" . PHP_EOL;
        foreach ($consts as $key => $val)  {
            echo "$key : $val" . PHP_EOL;
        }

        $props = $ref->getDefaultProperties(); //返回类中所有属性
        echo "--------------------props:--------------" . PHP_EOL . PHP_EOL;
        foreach ($props as $key => $val) {
            echo "$key : $val" . PHP_EOL;  // 属性名和属性值
        }

        $methods = $ref->getMethods();   //返回类中所有方法
        echo "-----------------methods:---------------" . PHP_EOL . PHP_EOL;
        foreach ($methods as $method) {
            echo $method->getName() . PHP_EOL;
        }
    }
}