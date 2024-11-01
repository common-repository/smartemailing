<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Test\Mock;

use SmartemailingDeps\SmartEmailing\Api\Model\Bag\AbstractBag;
class AbstractBagMock extends AbstractBag
{
    public function add(AbstractModelMock $model) : AbstractBagMock
    {
        $this->insertEntry($model);
        return $this;
    }
    public function create(string $name, string $value) : AbstractModelMock
    {
        $model = (new AbstractModelMock())->setName($name)->setValue($value);
        $this->add($model);
        return $model;
    }
}
