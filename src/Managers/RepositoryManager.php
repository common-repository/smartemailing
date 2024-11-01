<?php

namespace Smartemailing\Managers;

use Smartemailing\Repositories\OrderRepository;
use Smartemailing\Repositories\ProductRepository;
use SmartemailingDeps\Wpify\Model\Manager;

class RepositoryManager {
	public function __construct(
		Manager $manager,
		OrderRepository $orderRepository,
		ProductRepository $productRepository
	) {
		$manager->register_repository($orderRepository);
		$manager->register_repository($productRepository);

	}
}
