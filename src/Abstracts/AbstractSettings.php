<?php

namespace Smartemailing\Abstracts;

use Smartemailing\Integrations\SmartEmailingApi;
use SmartemailingDeps\Wpify\PluginUtils\PluginUtils;
use WP_Term;
use Smartemailing\Repositories\SettingsRepository;
use SmartemailingDeps\Wpify\CustomFields\CustomFields;

abstract class AbstractSettings {
	public function __construct(
		public CustomFields $wcf,
		public SettingsRepository $settings_repository,
		public SmartEmailingApi $smart_emailing_api,
		public PluginUtils $utils
	) {
		$this->setup();
	}

	abstract public function setup();

}
