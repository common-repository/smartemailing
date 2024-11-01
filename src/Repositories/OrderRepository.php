<?php

namespace Smartemailing\Repositories;

use Smartemailing\Models\OrderModel;
use SmartemailingDeps\Wpify\Model\OrderRepository as AbstractOrderRepository;

/**
 * @method OrderModel get()
 */
class OrderRepository extends AbstractOrderRepository {
	/**
	 * @inheritDoc
	 */
	public function model(): string {
		return OrderModel::class;
	}
}
