<?php

namespace Smartemailing\Managers;

use Smartemailing\Blocks\DownloaderBlock;
use Smartemailing\Blocks\WebformBlock;

class BlocksManager {
	public function __construct(
		WebformBlock $webform_block,
		DownloaderBlock $downloader_block
	) {
	}
}
