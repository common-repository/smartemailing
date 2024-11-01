<?php

namespace Smartemailing\Settings;

use Smartemailing\Abstracts\AbstractSettings;
use Smartemailing\Integrations\SmartEmailingApi;
use Smartemailing\Settings;
use Smartemailing\Templates;

class GeneralSettings extends AbstractSettings {
	const SETTINGS_VERSION = '1.0.0';
	const GUID = 'guid';
	const ENABLE_TRACKING = 'enable_tracking';
	const DISABLE_TRACKING_BY_COOKIE = 'disable_tracking_by_cookie';
	const TYPE = 'type';
	const VALUE = 'name';
	const NAME = 'value';
	const ESHOP_NAME = 'eshop_name';
	const API_USERNAME = 'api_username';
	const API_KEY = 'api_key';
	const UPDATE_LISTS = 'update_lists';
	const DEFAULT_LIST_IDS = 'default_list_ids';
	const ORDER_STATUSES = 'order_statuses';
	const WC_STATUS = 'wc_status';
	const SE_STATUS = 'se_status';
	const SHOW_CHECKBOX = 'show_checkbox';
	const CHECKBOX_CHECKED = 'checkbox_checked';
	const SUBSCRIPTION_TEXT = 'subscription_text';
	const CUSTOMER_BIRTHDAY_FIELD_NAME = 'customer_birthday_field_name';
	const CUSTOMER_NAME_DAY_FIELD_NAME = 'customer_name_day_field_name';
	const UPLOAD_ORDER_ONLY_FOR_EXISTING_CUSTOMERS = 'upload_order_only_for_existing_customers';
	const BULK_UPLOAD_EXISTING_CUSTOMERS = 'bulk_upload_existing_customers';
	const BULK_UPLOAD_EXISTING_ORDERS = 'bulk_upload_existing_orders';

	const FORM_STYLE = 'form_style';

	/**
	 * Setup.
	 * @return void
	 */
	public function setup() {
		add_action( 'admin_init', [ $this, 'sync_settings' ] );

		if ( $this->is_settings_page() ) {
			if ( $this->settings_repository->get_general_setting( self::API_USERNAME ) && $this->settings_repository->get_general_setting( self::API_KEY ) ) {
				$account_info = $this->smart_emailing_api->get_account_info();
				if ( ! is_wp_error( $account_info ) ) {
					$settings         = $this->settings_repository->get_general_settings();
					$settings['guid'] = $account_info['guid'];
					update_option( Settings::SECTION_GENERAL, $settings );
				}
			}
		}
		$items = array(
			array(
				'label' => __( 'API username', 'smartemailing' ),
				'desc'  => sprintf( __( 'You can find your API username <a href="%s" target="_blank">here</a>', 'smartemailing' ), 'https://app.smartemailing.cz/userinfo/api-keys/' ),
				'id'    => self::API_USERNAME,
				'type'  => 'text',
			),
			array(
				'label' => __( 'API key', 'smartemailing' ),
				'desc'  => sprintf( __( 'You can find your API key <a href="%s" target="_blank">here</a>', 'smartemailing' ), 'https://app.smartemailing.cz/userinfo/api-keys/' ),
				'id'    => self::API_KEY,
				'type'  => 'text',
			),
			array(
				'label' => __( 'E-shop name', 'smartemailing' ),
				'id'    => self::ESHOP_NAME,
				'type'  => 'text',
			),
			array(
				'label' => __( 'Enable frontend tracking', 'smartemailing' ),
				'id'    => self::ENABLE_TRACKING,
				'type'  => 'toggle',
			),
			array(
				'type'  => 'title',
				'label' => __( 'Disable tracking by cookie', 'smartemailing' ),
				'desc'  => __( 'You need consent from the visitor for marketing cookies. If you don`t enter the name and value of the marketing cookie data will be sent as if consent had been given.', 'smartemailing' ),
			),
			array(
				'label' => __( 'Cookie rules', 'smartemailing' ),
				'id'    => self::DISABLE_TRACKING_BY_COOKIE,
				'type'  => 'multi_group',
				'items' => array(
					array(
						'label'       => __( 'Rule type', 'smartemailing' ),
						'description' => __( 'Select when trekking should be prohibited.', 'smartemailing' ),
						'id'          => self::TYPE,
						'type'        => 'select',
						'options'     => array(
							array(
								'label' => __( 'If the cookie has a value', 'smartemailing' ),
								'value' => 'has_value',
							),
							array(
								'label' => __( 'If the cookie does not have a value', 'smartemailing' ),
								'value' => 'not_have_value',
							),
						),
						'default'     => 'not_have_value',
					),
					array(
						'label'       => __( 'Cookie name', 'smartemailing' ),
						'description' => __( 'Enter the name of the cookie.', 'smartemailing' ),
						'id'          => self::NAME,
						'type'        => 'text',
					),
					array(
						'label'       => __( 'Cookie value', 'smartemailing' ),
						'description' => __( 'Enter the value of the cookie.', 'smartemailing' ),
						'id'          => self::VALUE,
						'type'        => 'text',
					),
				),
			),
			array(
				'label' => __( 'Update lists', 'smartemailing' ),
				'id'    => self::UPDATE_LISTS,
				'type'  => 'button',
				'url'   => add_query_arg( array( 'action' => 'se_update_lists' ), admin_url() ),
			),

			array(
				'id'    => 'form_settings_title',
				'type'  => 'title',
				'title' => __( 'Form settings', 'smartemaling' ),
			),
			array(
				'id'      => self::FORM_STYLE,
				'type'    => 'select',
				'title'   => __( 'Form style', 'smartemaling' ),
				'options' => [
					[
						'label' => __( 'SmartEmailing', 'smartemaling' ),
						'value' => Templates::RENDER_SMARTEMAILING,
					],
					[
						'label' => __( 'Wordpress', 'smartemaling' ),
						'value' => Templates::RENDER_WORDPRESS,
					],
				],
			),
		);

		if ( smartemailing_plugin_is_active( 'woocommerce/woocommerce.php' ) ) {
			$woo_settings = array(
				array(
					'title' => __( 'WooCommerce Settings', 'smartemailing' ),
					'id'    => 'title_woocommerce',
					'type'  => 'title',
					'',
				),
				array(
					'title'   => __( 'Default lists', 'smartemailing' ),
					'desc'    => __( 'Subscribers will be subscribed by default into the selected lists. You can add product specific lists in the product settings.', 'smartemailing' ),
					'id'      => self::DEFAULT_LIST_IDS,
					'type'    => 'multi_select',
					'options' => function () {
						return $this->smart_emailing_api->get_lists_options();
					},
					'',
				),
				array(
					'label'       => __( 'Order statuses', 'smartemailing' ),
					'description' => __( 'To pair WooCommerce order statuses to SmartEmailing statuses. Default is "placed" when not specified.', 'smartemailing' ),
					'id'          => self::ORDER_STATUSES,
					'type'        => 'multi_group',
					'items'       => array(
						array(
							'label'   => __( 'WooCommerce order status', 'smartemailing' ),
							'id'      => self::WC_STATUS,
							'type'    => 'select',
							'options' => function () {
								$statuses = wc_get_order_statuses();

								return array_map( function ( $label, $value ) {
									return array(
										'label' => $label,
										'value' => $value,
									);
								}, $statuses, array_keys( $statuses ) );
							},
						),
						array(
							'label'   => __( 'SmartEmailing order status', 'smartemailing' ),
							'id'      => self::SE_STATUS,
							'type'    => 'select',
							'options' => function () {
								return SmartEmailingApi::get_order_status_options();
							},
						),
					),
				),
				array(
					'title' => __( 'Show checkbox', 'smartemailing' ),
					'desc'  => __( 'Check to show checkbox on checkout', 'smartemailing' ),
					'id'    => self::SHOW_CHECKBOX,
					'type'  => 'toggle',
				),
				array(
					'title' => __( 'Checkbox checked by default', 'smartemailing' ),
					'desc'  => __( 'Select to check the checkbox by default', 'smartemailing' ),
					'id'    => self::CHECKBOX_CHECKED,
					'type'  => 'toggle',
				),
				array(
					'title'   => __( 'Subscription text', 'smartemailing' ),
					'desc'    => __( 'Enter the subscription text on checkout', 'smartemailing' ),
					'id'      => self::SUBSCRIPTION_TEXT,
					'type'    => 'text',
					'default' => __( "I'd like to receive news", 'smartemailing' ),
				),
				array(
					'title' => __( 'Custom fields', 'smartemailing' ),
					'type'  => 'title',
				),
				array(
					'title' => __( "Customer's birthday field", 'smartemailing' ),
					'desc'  => __( "Meta field name for customer's birthday date", 'smartemailing' ),
					'id'    => self::CUSTOMER_BIRTHDAY_FIELD_NAME,
					'type'  => 'text',
				),
				array(
					'title' => __( "Customer's name-day field", 'smartemailing' ),
					'desc'  => __( "Meta field name for customer's name-day date", 'smartemailing' ),
					'id'    => self::CUSTOMER_NAME_DAY_FIELD_NAME,
					'type'  => 'text',
				),
				array(
					'type'  => 'title',
					'label' => __( 'Bulk uploads', 'smartemailing' ),
				),
				array(
					'label'   => __( 'Upload order only for existing customers', 'smartemailing' ),
					'id'      => self::UPLOAD_ORDER_ONLY_FOR_EXISTING_CUSTOMERS,
					'type'    => 'toggle',
					'default' => false,
				),
				array(
					'id'          => self::BULK_UPLOAD_EXISTING_CUSTOMERS,
					'type'        => 'button',
					'url'         => add_query_arg( array( 'action' => 'smartemailing_bulk_upload_customers' ), admin_url() ),
					'title'       => __( 'Bulk upload existing customers', 'smartemaling' ),
					'description' => __(
						'Bulk action will upload ALL existing customers regardless of consent. <strong>The settings above will be used (List ID, fields), please make sure to save the settings first before clicking on the Bulk upload button.</strong> The users will be uploaded in background, in batches of 500.',
						'smartemaling'
					),
				),
				array(
					'id'          => self::BULK_UPLOAD_EXISTING_ORDERS,
					'type'        => 'button',
					'url'         => add_query_arg( array( 'action' => 'smartemailing_bulk_upload_orders' ), admin_url() ),
					'title'       => __( 'Bulk upload existing orders', 'smartemaling' ),
					'description' => __(
						'Bulk action will upload ALL existing orders regardless of consent. Or only orders for existing customers in SmartEmailing, if toggle "Upload order only for existing customers" is on and <strong>saved before upload</strong>.<br/>The orders will be uploaded in background, in batches of 100.'
						, 'smartemaling'
					),
				),
			);

			$items = array_merge( $items, $woo_settings );
		}

		$this->wcf->create_options_page(
			array(
				'id'         => 'smartemailing',
				'page_title' => __( 'SmartEmailing', 'smartemailing' ),
				'menu_title' => __( 'SmartEmailing', 'smartemailing' ),
				'menu_slug'  => 'smartemailing',
				'icon_url'   => $this->utils->get_plugin_url( 'assets/icon.png' ),
				'items'      => array(
					array(
						'type'  => 'group',
						'id'    => Settings::SECTION_GENERAL,
						'items' => $items,
					),
				),
			)
		);
	}

	/**
	 * Sync settings from old plugin.
	 * @return void
	 */
	public function sync_settings(): void {
		if ( ! get_option( 'smartemailing_settings_version' ) || get_option( 'smartemailing_settings_version' ) < self::SETTINGS_VERSION ) {
			$settings = $this->settings_repository->get_general_settings();
			if ( empty( $this->settings_repository->get_general_setting( self::API_USERNAME ) ) ) {
				$settings[ self::API_USERNAME ] = get_option( 'smartemailing-username', '' );
			}
			if ( empty( $this->settings_repository->get_general_setting( self::API_KEY ) ) ) {
				$settings[ self::API_KEY ] = get_option( 'smartemailing-password', '' );
			}
			update_option( 'smartemailing_settings_version', self::SETTINGS_VERSION );
			update_option( Settings::SECTION_GENERAL, $settings );
		}
	}

	public function is_settings_page() {
		return is_admin() && isset( $_GET['page'] ) && $_GET['page'] === 'smartemailing';
	}
}
