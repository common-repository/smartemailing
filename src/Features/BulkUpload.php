<?php

namespace Smartemailing\Features;

use Exception;
use Smartemailing\Integrations\SmartEmailingApi;
use Smartemailing\Repositories\SettingsRepository;
use Smartemailing\Settings\GeneralSettings;
use WC_Customer;

class BulkUpload {
	const OPTION_CUSTOMERS_UPLOAD_PENDING_KEY = 'smartemailing_customers_upload_pending';
	const OPTION_ORDERS_UPLOAD_PENDING_KEY = 'smartemailing_orders_upload_pending';
	const USERS_BULK_LIMIT = 500;
	const ORDERS_BULK_LIMIT = 100;

	private SmartEmailingApi $smart_emailing_api;
	private SettingsRepository $settings_repository;

	public function __construct(
		SmartEmailingApi $smart_emailing_api,
		SettingsRepository $settings_repository
	) {
		$this->smart_emailing_api  = $smart_emailing_api;
		$this->settings_repository = $settings_repository;

		// Customers.
		add_action( 'admin_action_smartemailing_bulk_upload_customers', array( $this, 'schedule_customers_upload' ) );
		add_action( 'smartemailing_bulk_import_customers', array( $this, 'bulk_import_customers' ) );
		add_action( 'smartemailing_bulk_import_customers_finished', array( $this, 'finish_bulk_import_customers' ) );
		add_action( 'admin_notices', array( $this, 'render_pending_customers_bulk_upload_notice' ) );
		// Orders.
		add_action( 'admin_action_smartemailing_bulk_upload_orders', array( $this, 'schedule_orders_upload' ) );
		add_action( 'smartemailing_bulk_import_orders', array( $this, 'bulk_import_orders' ) );
		add_action( 'smartemailing_bulk_import_orders_finished', array( $this, 'finish_bulk_import_orders' ) );
		add_action( 'admin_notices', array( $this, 'render_pending_orders_bulk_upload_notice' ) );
		// Notices
		add_action( 'admin_notices', array( $this, 'maybe_show_notice' ) );
	}

	/**
	 * Schedule customers upload.
	 *
	 * @return void
	 */
	public function schedule_customers_upload(): void {
		if ( get_option( self::OPTION_CUSTOMERS_UPLOAD_PENDING_KEY ) ) {
			$message = 'already-run';
		} else {
			update_option( self::OPTION_CUSTOMERS_UPLOAD_PENDING_KEY, 1 );
			$roles = apply_filters( 'smartemailing_bulk_import_users_role', [] );
			for ( $i = 1; $i < PHP_INT_MAX; $i ++ ) {
				$args = array(
					'number' => self::USERS_BULK_LIMIT,
					'fields' => 'ID',
					'paged'  => $i,
				);
				if ( ! empty( $roles ) ) {
					$args['role__in'] = $roles;
				}
				$users = get_users( $args );
				if ( empty( $users ) ) {
					as_schedule_single_action( time(), 'smartemailing_bulk_import_customers_finished', array(), 'smartemailing' );
					break;
				}

				as_schedule_single_action( time(), 'smartemailing_bulk_import_customers', array( 'user_ids' => $users ), 'smartemailing' );
			}

			$message = 'success';
		}

		$return_url = add_query_arg( array(
			'se-bulk-action' => 'customers',
			'run'            => $message,
		), $this->settings_repository->get_settings_url() );

		wp_safe_redirect( $return_url, 302, 'SmartEmailing' );
		exit();
	}

	/**
	 * Bulk import customers data.
	 *
	 * @throws Exception
	 */
	public function bulk_import_customers( array $user_ids ): void {
		$list_ids = $this->settings_repository->get_general_setting( GeneralSettings::DEFAULT_LIST_IDS );
		foreach ( $user_ids as $user_id ) {
			$customer = new WC_Customer( $user_id );

			$this->smart_emailing_api->subscribe( $this->smart_emailing_api->get_customer_data( $customer, $list_ids ) );
		}
	}

	/**
	 * Finish bulk import.
	 *
	 * @return void
	 */
	public function finish_bulk_import_customers(): void {
		delete_option( self::OPTION_CUSTOMERS_UPLOAD_PENDING_KEY );
	}

	/**
	 * Render customers pending upload notice.
	 *
	 * @return void
	 */
	public function render_pending_customers_bulk_upload_notice(): void {
		if ( get_option( self::OPTION_CUSTOMERS_UPLOAD_PENDING_KEY ) ) {
			?>
            <div class="notice notice-warning">
                <p><?php _e( 'The bulk upload of customers to SmartEmailing is pending.', 'smartemailing' ); ?></p>
            </div>
			<?php
		}
	}

	/**
	 * Schedule orders upload.
	 *
	 * @return void
	 */
	public function schedule_orders_upload(): void {
		if ( get_option( self::OPTION_ORDERS_UPLOAD_PENDING_KEY ) ) {
			$message = 'already-run';
		} else {
			update_option( self::OPTION_ORDERS_UPLOAD_PENDING_KEY, 1 );

			for ( $i = 1; $i < PHP_INT_MAX; $i ++ ) {
				$args   = array(
					'type'   => 'shop_order',
					'limit'  => self::ORDERS_BULK_LIMIT,
					'return' => 'ids',
					'paged'  => $i,
				);
				$orders = wc_get_orders( $args );
				if ( empty( $orders ) ) {
					as_schedule_single_action( time(), 'smartemailing_bulk_import_orders_finished', array(), 'smartemailing' );
					break;
				}

				as_schedule_single_action( time(), 'smartemailing_bulk_import_orders', array( 'order_ids' => $orders ), 'smartemailing' );
			}

			$message = 'success';
		}

		$return_url = add_query_arg( array(
			'se-bulk-action' => 'orders',
			'run'            => $message,
		), $this->settings_repository->get_settings_url() );

		wp_safe_redirect( $return_url, 302, 'SmartEmailing' );
		exit();
	}

	/**
	 * Bulk import orders data.
	 *
	 * @throws Exception
	 */
	public function bulk_import_orders( array $order_ids ): void {
		$only_existing_customers = false;
		$emails                  = array();
		if ( $this->settings_repository->get_general_setting( GeneralSettings::UPLOAD_ORDER_ONLY_FOR_EXISTING_CUSTOMERS, false ) ) {
			$only_existing_customers = true;
			$emails                  = $this->get_customers_emails();
		}

		foreach ( $order_ids as $order_id ) {
			$order = wc_get_order( $order_id );

			if ( $only_existing_customers && ! in_array( $order->get_billing_email(), $emails ) ) {
				continue;
			}

			$smart_emailing_order = $this->smart_emailing_api->get_order( $order );
			if ( $smart_emailing_order ) {
				$this->smart_emailing_api->send_order( $smart_emailing_order );
			}
		}
	}

	/**
	 * Get existing customers e-mails.
	 *
	 * @return array
	 */
	public function get_customers_emails(): array {
		return array_map( function ( $customer ) {
			return $customer->emailaddress;
		}, $this->smart_emailing_api->get_customers() );
	}

	/**
	 * Finish bulk import.
	 *
	 * @return void
	 */
	public function finish_bulk_import_orders(): void {
		delete_option( self::OPTION_ORDERS_UPLOAD_PENDING_KEY );
	}

	/**
	 * Render orders pending upload notice.
	 *
	 * @return void
	 */
	public function render_pending_orders_bulk_upload_notice(): void {
		if ( get_option( self::OPTION_ORDERS_UPLOAD_PENDING_KEY ) ) {
			?>
            <div class="notice notice-warning">
                <p><?php _e( 'The bulk upload of orders to SmartEmailing is pending.', 'smartemailing' ); ?></p>
            </div>
			<?php
		}
	}

	/**
	 * Show admin notices
	 */
	public function maybe_show_notice(): void {
		$process = $_GET['se-bulk-action'] ?? null;
		$run     = $_GET['run'] ?? null;

		if ( empty( $process ) || empty( $run ) ) {
			return;
		}

		$string      = '';
		$notice_type = 'success';
		$process     = match ( $process ) {
			'customers' => __( 'bulk upload of customers', 'smartemailing' ),
			'orders' => __( 'bulk upload of orders', 'smartemailing' ),
		};

		switch ( $run ) {
			case 'success':
				$string = sprintf( __( 'The action for %s was scheduled and will be running in the background soon.', 'smartemailing' ), $process );
				break;
			case 'already-run':
				$string      = sprintf( __( 'The action for %s is already scheduled and pending to run. It may take some time, please wait.', 'wpify-woo-fakturoid' ), $process );
				$notice_type = 'warning';
				break;
		}

		if ( $string ) {
			printf( '<div class="notice-%s notice"><p>%s</p></div>', $notice_type, $string );
		}
	}
}
