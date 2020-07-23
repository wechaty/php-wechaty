<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/23
 * Time: 11:38 AM
 */

namespace IO\Github\Wechaty\Puppet\FileBox;


class FileBoxType extends FileBoxOptions {
    public $code;

    const UNKNOWN = 0;

    const BASE64 = 1;
    const URL = 2;
    const QRCODE = 3;

    const BUFFER = 4;
    const FILE = 5;
    const STREAM = 6;
}