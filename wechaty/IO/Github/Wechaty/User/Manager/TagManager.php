<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/21
 * Time: 8:53 PM
 */
namespace IO\Github\Wechaty\User\Manager;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\User\Tag;

class TagManager extends Accessory {
    const CACHE_TAG_PREFIX = "ct_";

    function load(String $id) : Tag {
        $tag = $this->_cache->get(self::CACHE_TAG_PREFIX . $id);
        if (empty($tag)) {
            $tag = new Tag($this->wechaty, $id);
        }
        $this->_cache->set(self::CACHE_TAG_PREFIX . $id, $tag);
        return $tag;
    }
}