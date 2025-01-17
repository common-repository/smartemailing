<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Bag;

use SmartemailingDeps\SmartEmailing\Api\Model\Contact\ContactList;
class ContactListBag extends AbstractBag
{
    public function add(ContactList $model) : ContactListBag
    {
        $this->insertEntry($model);
        return $this;
    }
    public function create(int $id, string $status) : ContactList
    {
        $model = new ContactList($id, $status);
        $this->add($model);
        return $model;
    }
}
