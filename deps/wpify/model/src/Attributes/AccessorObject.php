<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
declare (strict_types=1);
namespace SmartemailingDeps\Wpify\Model\Attributes;

use Attribute;
use SmartemailingDeps\Wpify\Model\Interfaces\AccessorAttributeInterface;
use SmartemailingDeps\Wpify\Model\Interfaces\ModelInterface;
#[Attribute(Attribute::TARGET_PROPERTY)]
class AccessorObject implements AccessorAttributeInterface
{
    public function __construct(private ?string $key = null, private string $getter = '', private string $setter = '')
    {
    }
    public function get(ModelInterface $model, string $key) : mixed
    {
        $key = $this->key ?? $key;
        $source = $model->source();
        if ($this->getter) {
            $getter = $this->getter;
        } else {
            $getter = \sprintf('get_%s', $key);
        }
        if (\method_exists($source, $getter)) {
            $value = $source->{$getter}();
        } elseif (\method_exists($source, $key)) {
            $value = $source->{$key}();
        } else {
            $value = null;
        }
        return $value;
    }
    public function set(ModelInterface $model, string $key, mixed $value) : mixed
    {
        $key = $this->key ?? $key;
        $source = $model->source();
        if ($this->setter) {
            $setter = $this->setter;
        } else {
            $setter = \sprintf('set_%s', $key);
        }
        if (\method_exists($source, $setter)) {
            $source = $source->{$setter}($value);
        } elseif (\method_exists($source, $key)) {
            $source = $source->{$key}($value);
        } else {
            return null;
        }
        return $source;
    }
}
