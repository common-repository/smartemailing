<?php

declare (strict_types=1);
namespace SmartemailingDeps\Wpify\Model;

use SmartemailingDeps\Wpify\Model\Attributes\TermPostsRelation;
class ProductCat extends Term
{
    /**
     * Products assigned to this tag.
     *
     * @var Post[]
     */
    #[TermPostsRelation(Product::class)]
    public array $products = array();
}
