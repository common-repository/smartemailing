<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
declare (strict_types=1);
namespace SmartemailingDeps\Wpify\Model\Attributes;

use Attribute;
use SmartemailingDeps\Wpify\Model\Interfaces\ModelInterface;
use SmartemailingDeps\Wpify\Model\Interfaces\SourceAttributeInterface;
use SmartemailingDeps\Wpify\Model\Order;
use SmartemailingDeps\Wpify\Model\OrderItemFee;
use SmartemailingDeps\Wpify\Model\OrderItemLine;
use SmartemailingDeps\Wpify\Model\OrderItemShipping;
#[Attribute(Attribute::TARGET_PROPERTY)]
class OrderItemsRelation implements SourceAttributeInterface
{
    public function __construct(private string $order_item_type = 'line_item')
    {
    }
    /**
     * Gets order items from the order.
     *
     * @param Order $model
     * @param string $key
     *
     * @return mixed
     * @throws \Wpify\Model\Exceptions\RepositoryNotFoundException
     */
    public function get(ModelInterface $model, string $key) : mixed
    {
        $manager = $model->manager();
        $class = match ($this->order_item_type) {
            'line_item' => OrderItemLine::class,
            'shipping' => OrderItemShipping::class,
            'fee' => OrderItemFee::class,
        };
        $repository = $manager->get_model_repository($class);
        $items = array();
        foreach ($model->wc_order->get_items($this->order_item_type) as $item) {
            $items[] = $repository->get($item);
        }
        return $items;
    }
}
