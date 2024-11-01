<?php
/*
 * Plugin Name:          SmartEmailing
 * Description:          SmartEmailing for WooCommerce
 * Version:              2.2.0
 * Requires PHP:         8.0.0
 * Requires at least:    6.0.0
 * Author:               SmartEmailing.cz
 * Author URI:           https://www.smartemailing.cz
 * License:              GPL v2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          smartemailing
 * Domain Path:          /languages
 * WC requires at least: 6.0
 * WC tested up to:      7.9
*/

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Smartemailing\Plugin;
use SmartemailingDeps\DI\Container;
use SmartemailingDeps\DI\ContainerBuilder;

if ( ! defined( 'SMARTEMAILING_MIN_PHP_VERSION' ) ) {
	define( 'SMARTEMAILING_MIN_PHP_VERSION', '8.0.0' );
}

/**
 * @return Plugin
 * @throws Exception
 */
function smartemailing(): Plugin {
	return smartemailing_container()->get( Plugin::class );
}

/**
 * @return Container
 * @throws Exception
 */
function smartemailing_container(): Container {
	static $container;

	if ( empty( $container ) ) {
		$is_production    = ! WP_DEBUG;
		$file_data        = get_file_data( __FILE__, array( 'version' => 'Version' ) );
		$definition       = require_once __DIR__ . '/config.php';
		$containerBuilder = new ContainerBuilder();
		$containerBuilder->addDefinitions( $definition );
		$container = $containerBuilder->build();
	}

	return $container;
}

function smartemailing_activate( $network_wide ) {
	smartemailing()->activate( $network_wide );
}

function smartemailing_deactivate( $network_wide ) {
	smartemailing()->deactivate( $network_wide );
}

function smartemailing_uninstall() {
	smartemailing()->uninstall();
}

function smartemailing_php_upgrade_notice() {
	$info = get_plugin_data( __FILE__ );

	$string = sprintf(
		__( 'Opps! %s requires a minimum PHP version of %s. Your current version is: %s. Please contact your host to upgrade.', 'smartemailing' ),
		$info['Name'],
		SMARTEMAILING_MIN_PHP_VERSION,
		PHP_VERSION
	);
	printf( '<div class="error notice"><p>%s</p></div>', $string);
}



function smartemailing_load_textdomain() {
	load_plugin_textdomain( 'smartemailing', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', 'smartemailing_load_textdomain' );


function smartemailing_plugin_is_active( $plugin ) {
	if ( is_multisite() ) {
		$plugins = get_site_option('active_sitewide_plugins');
		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}
	}

	if ( in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ) {
		return true;
	}

	return false;
}


if ( version_compare( PHP_VERSION, SMARTEMAILING_MIN_PHP_VERSION ) < 0 ) {
	add_action( 'admin_notices', 'smartemailing_php_upgrade_notice' );
} else {
	require_once __DIR__ . '/deps/scoper-autoload.php';
	require_once __DIR__ . '/deps/autoload.php';
	require_once __DIR__ . '/vendor/autoload.php';

	add_action( 'plugins_loaded', 'smartemailing', 11 );
	register_activation_hook( __FILE__, 'smartemailing_activate' );
	register_deactivation_hook( __FILE__, 'smartemailing_deactivate' );
	register_uninstall_hook( __FILE__, 'smartemailing_uninstall' );
}

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( FeaturesUtil::class ) ) {
		FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
