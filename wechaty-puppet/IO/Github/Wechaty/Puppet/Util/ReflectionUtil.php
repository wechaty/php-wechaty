<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/8/2
 * Time: 8:02 PM
 */
namespace IO\Github\Wechaty\Puppet\Util;

class ReflectionUtil {
    public static function getClassName($class) {
        if($class instanceof \ReflectionClass) {
           $ref = $class;
        } else {
            $ref = new \ReflectionClass($class);
        }

        $name = $ref->getName();

        return $name;
    }

    public static function getPropertiesValue($class, $isPublic = true) {
        if($class instanceof \ReflectionClass) {
            $ref = $class;
        } else {
            $ref = new \ReflectionClass($class);
        }

        $props = $ref->getProperties();
        $properties = array();
        foreach ($props as $key => $val) {
            if($isPublic) {
                if($val->isPublic()) {
                    $properties[$val->getName()] = $val->getValue($class);
                }
            } else {
                $properties[$val->getName()] = $val->getValue($class);
            }
        }

        return $properties;
    }

    public static function getPublicProperties($class) {
        $ref = new \ReflectionClass($class);
        $props = $ref->getProperties();
        $properties = array();
        foreach ($props as $key => $val) {
            if($val->isPublic()) {
                $properties[] = $val->getName();
            }
        }

        return $properties;
    }

    public static function reflection($class) {
        $ref = new \ReflectionClass($class);

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