<?php

namespace Smartemailing;

use Smartemailing\Settings\GeneralSettings;
use Smartemailing\Plugin;

/**
 * Class Settings
 *
 * @package  Wpify\Settings
 * @property Plugin $plugin
 */
class Settings {
	const SECTION_GENERAL = 'smartemailing_general';

	public function __construct(
		GeneralSettings $general,
	) {
		add_action(
			'admin_init',
			function () {
				if ( filter_input( INPUT_GET, 'tab' ) === 'smartemailing' && is_null( filter_input( INPUT_GET, 'section' ) ) ) {
					wp_redirect(
						add_query_arg(
							array(
								'page'    => 'wc-settings',
								'tab'     => 'smartemailing',
								'section' => self::SECTION_GENERAL,
							),
							admin_url( 'admin.php' )
						)
					);
					exit();
				}
			}
		);
	}

}
