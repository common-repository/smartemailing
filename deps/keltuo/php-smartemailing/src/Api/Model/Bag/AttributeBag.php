<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Bag;

use SmartemailingDeps\SmartEmailing\Api\Model\Attribute;
class AttributeBag extends AbstractBag
{
    public function add(Attribute $model) : AttributeBag
    {
        $this->insertEntry($model);
        return $this;
    }
    public function create(string $name, string $value) : Attribute
    {
        $model = new Attribute($name, $value);
        $this->add($model);
        return $model;
    }
}
