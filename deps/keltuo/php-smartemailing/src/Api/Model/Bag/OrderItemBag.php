<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Bag;

use SmartemailingDeps\SmartEmailing\Api\Model\OrderItem;
use SmartemailingDeps\SmartEmailing\Api\Model\Price;
class OrderItemBag extends AbstractBag
{
    public function add(OrderItem $model) : OrderItemBag
    {
        $this->insertEntry($model);
        return $this;
    }
    public function create(string $id, string $name, Price $price, int $quantity, string $url) : OrderItem
    {
        $model = new OrderItem($id, $name, $price, $quantity, $url);
        $this->add($model);
        return $model;
    }
}
