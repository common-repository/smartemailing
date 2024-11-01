<?php

namespace Smartemailing\Managers;

use Smartemailing\Features\BulkUpload;
use Smartemailing\Features\FrontendTracking;
use Smartemailing\Features\Order;

class FeaturesManager {
	public function __construct(
		BulkUpload $bulk_upload,
		FrontendTracking $tracking,
		Order $order,
	) {
	}
}
