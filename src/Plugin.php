<?php

namespace Smartemailing;

use Smartemailing\Integrations\SmartEmailingApi;
use Smartemailing\Managers\BlocksManager;
use Smartemailing\Managers\FeaturesManager;
use Smartemailing\Managers\RepositoryManager;
use Smartemailing\Managers\WidgetsManager;
use Smartemailing\PostTypes\ProductPostType;
use Smartemailing\Settings;
use SmartemailingDeps\Wpify\Model\Manager;

class Plugin {
	public function __construct(
		RepositoryManager $repository_manager,
		FeaturesManager $features_manager,
		Manager $manager,
		Settings $settings,
		SmartEmailingApi $smart_emailing_api,
		ProductPostType $product_post_type,
		WidgetsManager $widgets_manager,
		BlocksManager $blocks_manager
	) {
		$this->setup();
	}

	public function setup() {
		add_filter( 'plugin_action_links_smartemailing/smartemailing.php', array( $this, 'add_action_links' ) );
	}


	public function add_action_links( $links ): array {
		$before = array(
			'settings' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=smartemailing' ), __( 'Settings', 'smartemailing' ) ),
		);

		return array_merge( $before, $links );
	}

	public function activate() {
	}

	public function deactivate() {
	}

	public function uninstall() {
	}

}
