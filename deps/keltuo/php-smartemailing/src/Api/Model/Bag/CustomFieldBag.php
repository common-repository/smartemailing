<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Bag;

use SmartemailingDeps\SmartEmailing\Api\Model\Contact\CustomField;
class CustomFieldBag extends AbstractBag
{
    public function add(CustomField $model) : CustomFieldBag
    {
        $this->insertEntry($model);
        return $this;
    }
    public function create(int $id, string $value, array $options = []) : CustomField
    {
        $model = new CustomField($id, $options, $value);
        $this->add($model);
        return $model;
    }
}
