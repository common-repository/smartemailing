<?php

declare (strict_types=1);
namespace SmartemailingDeps\Wpify\Model\Interfaces;

interface SourceAttributeInterface
{
    public function get(ModelInterface $model, string $key) : mixed;
}
