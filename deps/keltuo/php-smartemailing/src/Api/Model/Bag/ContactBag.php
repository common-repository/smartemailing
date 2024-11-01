<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Bag;

use SmartemailingDeps\SmartEmailing\Api\Model\Contact\ContactDetail;
class ContactBag extends AbstractBag
{
    public function add(ContactDetail $model) : ContactBag
    {
        $this->insertEntry($model);
        return $this;
    }
    public function create(string $emailAddress) : ContactDetail
    {
        $model = new ContactDetail($emailAddress);
        $this->add($model);
        return $model;
    }
}
