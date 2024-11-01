<?php

namespace Smartemailing\Repositories;

use Smartemailing\Models\ProductModel;
use SmartemailingDeps\Wpify\Model\OrderRepository as AbstractOrderRepository;

/**
 * @method ProductModel get()
 */
class ProductRepository extends \SmartemailingDeps\Wpify\Model\ProductRepository {
	public function model(): string {
		return ProductModel::class;
	}
}
