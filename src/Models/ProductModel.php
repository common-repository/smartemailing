<?php

namespace Smartemailing\Models;

use SmartemailingDeps\Wpify\Model\Product;
use SmartemailingDeps\Wpify\Model\Attributes\Meta;
class ProductModel extends Product {
	#[Meta]
	public ?array $smartemailing_lists;
}
