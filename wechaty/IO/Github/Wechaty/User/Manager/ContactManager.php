<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 12:31 PM
 */
namespace IO\Github\Wechaty\User\Manager;

use IO\Github\Wechaty\Accessory;
use IO\Github\Wechaty\Puppet\Cache\CacheFactory;
use IO\Github\Wechaty\User\Contact;
use IO\Github\Wechaty\User\ContactSelf;

class ContactManager extends Accessory {
    const CACHE_CONTACT_PREFIX = "cc_";

    private $_contactCache = null;

    public function __construct($wechaty) {
        parent::__construct($wechaty);

        $this->_contactCache = $this->_initCache();
    }

    function load(String $id) : Contact {
        $contact = $this->_contactCache->get(self::CACHE_CONTACT_PREFIX . $id);
        if(empty($contact)) {
            $contact = new Contact($this->wechaty, $id);
        }
        return $contact;
    }

    function loadSelf(String $id) : ContactSelf {
        $contactSelf = new ContactSelf($this->wechaty, $id);
        $this->_contactCache->set(self::CACHE_CONTACT_PREFIX . $id, $contactSelf);
        return $contactSelf;
    }
}