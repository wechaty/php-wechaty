<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/24
 * Time: 8:33 PM
 */
namespace IO\Github\Wechaty\Type;

use IO\Github\Wechaty\User\Contact;

interface Sayable {
    function saySomething($something, Contact $contact);
}
