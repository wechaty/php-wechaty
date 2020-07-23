<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 7:56 AM
 */

namespace IO\Github\Wechaty\Puppet\Schemas;

class ImageType {
    private $code;

    const UNKNOWN = 0;
    const THUMBNAIL = 1;
    const HD = 2;
    const ARTWORK = 3;
}