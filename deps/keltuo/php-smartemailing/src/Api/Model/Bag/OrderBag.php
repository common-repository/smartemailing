<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Bag;

use SmartemailingDeps\SmartEmailing\Api\Model\Order;
class OrderBag extends AbstractBag
{
    public function add(Order $model) : OrderBag
    {
        $this->insertEntry($model);
        return $this;
    }
    public function create(string $emailAddress, string $eshopName, string $eshopCode) : Order
    {
        $model = new Order($emailAddress, $eshopName, $eshopCode);
        $this->add($model);
        return $model;
    }
}
