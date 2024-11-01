<?php

namespace Smartemailing\Integrations;

use Exception;
use Smartemailing\PostTypes\ProductPostType;
use Smartemailing\Repositories\ProductRepository;
use Smartemailing\Repositories\SettingsRepository;
use Smartemailing\Settings\GeneralSettings;
use SmartemailingDeps\SmartEmailing\Api\Model\Attribute;
use SmartemailingDeps\SmartEmailing\Api\Model\Bag\AttributeBag;
use SmartemailingDeps\SmartEmailing\Api\Model\Bag\ContactBag;
use SmartemailingDeps\SmartEmailing\Api\Model\Contact\ContactDetail;
use SmartemailingDeps\SmartEmailing\Api\Model\Contact\ContactList;
use SmartemailingDeps\SmartEmailing\Api\Model\Contact\Settings;
use SmartemailingDeps\SmartEmailing\Api\Model\Import;
use SmartemailingDeps\SmartEmailing\Api\Model\Order;
use SmartemailingDeps\SmartEmailing\Api\Model\OrderItem;
use SmartemailingDeps\SmartEmailing\Api\Model\Price;
use SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts;
use SmartemailingDeps\SmartEmailing\SmartEmailing;
use WC_Customer;
use WC_Order;
use WC_Order_Item_Product;
use WP_Error;

class SmartEmailingApi {
	const LISTS_OPTION_KEY = 'smartemailing_lists';
	const WEBFORMS_OPTION_KEY = 'smartemailing_webforms';
	const API_USERNAME = 'api_username';
	const API_KEY = 'api_key';
	const DEFAULT_ORDER_STATUS = 'placed';
	const DOWNLOAD_COUNT = 'download_count';
	const WEBFORM = 'webform';
	const EXPIRATION = 43200;

	private bool $initialized = false;
	/** @var ProductRepository */
	private ProductRepository $product_repository;
	/** @var SmartEmailing */
	private SmartEmailing $smart_emailing;
	/** @var SettingsRepository */
	private SettingsRepository $settings_repository;

	public function __construct(
		ProductRepository $product_repository,
		SettingsRepository $settings_repository
	) {
		$this->product_repository  = $product_repository;
		$this->settings_repository = $settings_repository;
		$this->initialize();

		add_action( 'admin_action_se_update_lists', array( $this, 'update_lists' ) );
		add_action( 'admin_notices', array( $this, 'maybe_show_notice' ) );
	}

	/**
	 * Update lists.
	 *
	 * @return void
	 */
	public function update_lists( $redirect = true ): void {
		if ( empty( $this->settings_repository->get_general_setting( self::API_KEY ) ) || empty( $this->settings_repository->get_general_setting( self::API_USERNAME ) ) ) {
			$message = __( 'The data for communication with the API is not filled in. Please fill in the API data first and save it.', 'smartemailing' );
		} else {
			$data = $this->load_lists();
			if ( is_wp_error( $data ) ) {
				$message = $data->get_error_message();
			} else {
				$lists = array_map( function ( $item ) {
					return (array) $item;
				}, $data );
				update_option( self::LISTS_OPTION_KEY, $lists, 'no' );
				$message = 'success';
			}
			$data = $this->load_webforms();
			if ( is_wp_error( $data ) ) {
				$message = $data->get_error_message();
			} else {
				$webforms = array_map( function ( $item ) {
					return (array) $item;
				}, $data );
				update_option( self::WEBFORMS_OPTION_KEY, $webforms, 'no' );
				$message = 'success';
			}
		}

		if ( ! $redirect ) {
			return;
		}
		$return_url = add_query_arg( array(
			'se-lists-updated' => $message,
		), $this->settings_repository->get_settings_url() );

		wp_safe_redirect( $return_url, 302, 'SmartEmailing' );
		exit();
	}

	/**
	 * Load lists.
	 *
	 * @return WP_Error|array
	 */
	public function get_account_info(): WP_Error|array {
		try {
			$api = new AccountInfo( $this->smart_emailing );

			return $api->getAccountInfo()->getData();
		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * Get customers.
	 *
	 * @return array
	 */
	public function get_customers(): array {
		$search = new Contacts();
		$search->selectBy( 'emailaddress' );

		return $this->smart_emailing->contacts()->getList( $search )->getData();
	}

	/**
	 * Load lists.
	 *
	 * @return WP_Error|array
	 */
	public function load_lists(): WP_Error|array {
		try {
			return $this->smart_emailing->contactLists()->getList()->getData();
		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	public function load_webforms() {
		try {
			return $this->smart_emailing->webForms()->getList()->getData();
		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * Get webforms.
	 *
	 * @return mixed
	 */
	public function get_webforms(): mixed {
		return get_option( self::WEBFORMS_OPTION_KEY, array() );
	}

	/**
	 * Get lists.
	 *
	 * @return mixed
	 */
	public function get_lists(): mixed {
		return get_option( self::LISTS_OPTION_KEY, array() );
	}

	/**
	 * Get list options.
	 *
	 * @return array
	 */
	public function get_lists_options(): array {
		return array_map( function ( $item ) {
			return [
				'label' => $item['name'],
				'value' => strval( $item['id'] ),
			];
		}, $this->get_lists() );
	}

	/**
	 * Get webforms options
	 *
	 * @return array
	 */
	public function get_webforms_options( $cached = true ): array {
		if ( ! $cached ) {
			$this->update_lists( false );
		}

		return array_map( function ( $item ) {
			return [
				'label' => $item['name'],
				'value' => strval( $item['id'] ),
			];
		}, $this->get_webforms() );
	}

	/**
	 * Get order list ID.
	 *
	 * @param WC_Order $order Order.
	 * @param array $default_list_ids Default list IDs.
	 * @param string $status Order status.
	 *
	 * @return array
	 */
	public function get_order_list_ids( WC_Order $order, array $default_list_ids = array(), string $status = '' ): array {
		if ( empty( $default_list_ids ) ) {
			$default_list_ids = $this->settings_repository->get_general_setting( GeneralSettings::DEFAULT_LIST_IDS, array() );
		}
		if ( '' === $status ) {
			$status = $order->get_status();
		}
		$list_ids = $default_list_ids;

		foreach ( $order->get_items() as $order_item ) {
			/** Order item. @var WC_Order_Item_Product $order_item */
			$product = $this->product_repository->get( $order_item->get_product_id() );
			if ( ! empty( $product->smartemailing_lists ) ) {
				foreach ( $product->smartemailing_lists as $item ) {
					if ( in_array( sprintf( 'wc-%s', $status ), $item[ ProductPostType::STATUSES ] ) ) {
						$list_ids = array_merge( $list_ids, $item[ ProductPostType::LISTS ] );
					}
				}
			}
		}

		return array_unique( $list_ids );
	}

	/**
	 * Get API data from WC order.
	 *
	 * @param WC_Order $order WC Order.
	 * @param array $list_ids List IDs.
	 *
	 * @return array
	 */
	public function get_customer_data_from_order( WC_Order $order, array $list_ids = array() ): array {
		$data = array(
			'email'      => $order->get_billing_email(),
			'first_name' => $order->get_billing_first_name(),
			'last_name'  => $order->get_billing_last_name(),
			'street'     => $order->get_billing_address_1(),
			'postcode'   => $order->get_billing_postcode(),
			'company'    => $order->get_billing_company(),
			'city'       => $order->get_billing_city(),
			'phone'      => $order->get_billing_phone(),
			'list_ids'   => $list_ids,
		);

		return array_merge( $data, $this->get_customer_custom_meta( $order->get_customer_id() ) );
	}

	/**
	 * Get API data from WC customer data.
	 *
	 * @param WC_Customer $customer Customer.
	 * @param array $list_ids Array of list IDs.
	 *
	 * @return array
	 */
	public function get_customer_data( WC_Customer $customer, array $list_ids = array() ): array {
		$data = array(
			'email'      => $customer->get_billing_email() ?: $customer->get_email(),
			'first_name' => $customer->get_billing_first_name(),
			'last_name'  => $customer->get_billing_last_name(),
			'street'     => $customer->get_billing_address_1(),
			'postcode'   => $customer->get_billing_postcode(),
			'company'    => $customer->get_billing_company(),
			'city'       => $customer->get_billing_city(),
			'phone'      => $customer->get_billing_phone(),
			'list_ids'   => $list_ids,
		);

		return array_merge( $data, $this->get_customer_custom_meta( $customer->get_id() ) );
	}

	/**
	 * Get customer custom meta data.
	 *
	 * @param int $customer_id Customer ID.
	 *
	 * @return array
	 */
	private function get_customer_custom_meta( int $customer_id ): array {
		$data = array();

		$birthday_field_name = $this->settings_repository->get_general_setting( GeneralSettings::CUSTOMER_BIRTHDAY_FIELD_NAME, '' );
		if ( '' !== $birthday_field_name ) {
			$data['birthday'] = get_user_meta( $customer_id, $birthday_field_name, true );
		}

		$name_dat_field_name = $this->settings_repository->get_general_setting( GeneralSettings::CUSTOMER_NAME_DAY_FIELD_NAME, '' );
		if ( '' !== $name_dat_field_name ) {
			$data['name_day'] = get_user_meta( $customer_id, $name_dat_field_name, true );
		}

		return $data;
	}

	/**
	 * Get SmartEmailing order.
	 *
	 * @param WC_Order $order WC Order.
	 *
	 * @return Order|false
	 */
	public function get_order( WC_Order $order ): Order|false {
		if ( ! filter_var( $order->get_billing_email(), FILTER_VALIDATE_EMAIL ) ) {
			return false;
		}

		$smart_emailing_order = new Order(
			$order->get_billing_email(),
			$this->settings_repository->get_general_setting( GeneralSettings::ESHOP_NAME ),
			$order->get_order_number(),
		);
		$smart_emailing_order
			->setCreatedAt( $order->get_date_created()->date( 'Y-m-d H:i:s' ) )
			->setStatus( $this->get_order_status( $order->get_status() ) );

		foreach ( $this->get_order_items( $order->get_items(), $order->get_currency() ) as $order_item ) {
			$smart_emailing_order->getOrderItemBag()
			                     ->add( $order_item );
		}

		return $smart_emailing_order;
	}

	/**
	 * Send customer data to API.
	 *
	 * @param array $data Data.
	 *
	 * @return array|null
	 */
	public function subscribe( array $data ): array|null {
		if ( ! filter_var( $data['email'], FILTER_VALIDATE_EMAIL ) ) {
			return null;
		}

		$contact = ( new ContactDetail( $data['email'] ) )
			->setName( $data['first_name'] )
			->setSurname( $data['last_name'] )
			->setStreet( $data['street'] )
			->setTown( $data['city'] )
			->setPostalCode( $data['postcode'] )
			->setCellphone( $data['phone'] )
			->setCompany( $data['company'] );

		if ( isset( $data['birthday'] ) && '' !== $data['birthday'] ) {
			$contact->setBirthday( $data['birthday'] );
		}
		if ( isset( $data['name_day'] ) && '' !== $data['name_day'] ) {
			$contact->setNameDay( $data['name_day'] );
		}

		foreach ( $data['list_ids'] as $list ) {
			$contact
				->getContactListBag()
				->add( new ContactList(
					$list,
					ContactList::CONFIRMED
				) );
		}

		$contact_bag = ( new ContactBag() )->add( $contact );

		$import_model = new Import(
			$contact_bag,
			new Settings(
				true,
				true,
				true,
				true,
				true,
				false
			)
		);

		return $this->smart_emailing->import()->contacts( $import_model )->getData();
	}

	/**
	 * Send order info to Smart emailing.
	 *
	 * @param Order $order SmartEmailing order.
	 *
	 * @return array|null
	 */
	public function send_order( Order $order ): array|null {
		if ( ! filter_var( $order->getEmailAddress(), FILTER_VALIDATE_EMAIL ) ) {
			return null;
		}

		return $this->smart_emailing->eshops()->createOrUpdateOrder( $order )->getData();
	}

	/**
	 * @param string $order_status
	 *
	 * @return string
	 */
	private function get_order_status( string $order_status ): string {
		foreach ( $this->settings_repository->get_general_setting( GeneralSettings::ORDER_STATUSES, array() ) as $item ) {
			if ( $item[ GeneralSettings::WC_STATUS ] === sprintf( 'wc-%s', $order_status ) ) {
				return $item[ GeneralSettings::SE_STATUS ];
			}
		}

		return self::DEFAULT_ORDER_STATUS;
	}

	/**
	 * Get order status options.
	 *
	 * @return array
	 */
	public static function get_order_status_options(): array {
		$options = array();
		foreach ( self::get_order_statuses() as $value => $label ) {
			$options[] = array(
				'label' => $label,
				'value' => $value,
			);
		}

		return $options;
	}

	/**
	 * Get order statuses.
	 *
	 * @return array
	 */
	public static function get_order_statuses(): array {
		return array(
			'placed'     => __( 'Placed', 'smartemailing' ),
			'processing' => __( 'Processing', 'smartemailing' ),
			'shipped'    => __( 'Shipped', 'smartemailing' ),
			'cancelled'  => __( 'Cancelled', 'smartemailing' ),
			'unknown'    => __( 'Unknown', 'smartemailing' ),
		);
	}

	/**
	 * Get order items.
	 *
	 * @param array $order_items WC order item.
	 * @param string $currency Currency.
	 *
	 * @return array
	 */
	private function get_order_items( array $order_items, string $currency ): array {
		$items = array();
		/** @var WC_Order_Item_Product $order_item */
		foreach ( $order_items as $order_item ) {
			$product = $order_item->get_product();
			if ( $product ) {
				$item = new OrderItem(
					$product->get_id(),
					$product->get_name(),
					new Price( wc_get_price_excluding_tax( $product ), wc_get_price_including_tax( $product ), $currency ),
					$order_item->get_quantity(),
					$product->get_permalink()
				);

				$attributes = new AttributeBag();
				$attr       = new Attribute( 'regular_price', $product->get_regular_price() );
				$attributes->add( $attr );
				$attr = new Attribute( 'sale_price', $product->get_sale_price() );
				$attributes->add( $attr );
				$attr = new Attribute( 'product_type', $product->get_type() );
				$attributes->add( $attr );
				$categories = get_the_terms( $product->get_id(), 'product_cat' );
				if ( $product->get_weight() ) {
					$attr = new Attribute( 'weight', $product->get_weight() );
					$attributes->add( $attr );
				}
				if ( $categories ) {
					foreach ( $categories as $category ) {
						$attr = new Attribute( 'category', $category->name );
						$attributes->add( $attr );
					}
				}

				$item->setAttributeBag( $attributes );
				$items[] = $item;
			}
		}

		return $items;
	}

	/**
	 * Initialize.
	 *
	 * @return void
	 */
	private function initialize() {
		if ( ! $this->initialized ) {
			$api_username = $this->settings_repository->get_general_setting( GeneralSettings::API_USERNAME );
			$api_key      = $this->settings_repository->get_general_setting( GeneralSettings::API_KEY );
			if ( $api_username && $api_key ) {
				$this->smart_emailing = new SmartEmailing( $api_username, $api_key );
				$this->initialized    = true;
			}
		}
	}

	/**
	 * Show admin notices.
	 *
	 * @return void
	 */
	public function maybe_show_notice(): void {
		$process = $_GET['se-lists-updated'] ?? null;

		if ( ! empty( $process ) ) {
			if ( $process !== 'success' ) {
				$string      = sprintf( __( 'Error: %s', 'smartemailing' ), $process );
				$notice_type = 'error';
			} else {
				$string      = __( 'SmartEmailing lists has been updated.', 'smartemailing' );
				$notice_type = 'success';
			}

			printf( '<div class="notice-%s notice"><p>%s</p></div>', $notice_type, $string );
		}
	}

	public function get_download_count( $contact_list_id ) {
		$transient = sprintf( '%s_%s', self::DOWNLOAD_COUNT, $contact_list_id );
		$data      = get_transient( $transient );
		if ( $data === false ) {
			$response = $this->smart_emailing->contactLists()->getAddedContacts( $contact_list_id );
			if ( $response->getStatus() === 'error' ) {
				return false;
			}

			$data = $response->getData();

			set_transient( $transient, $data, self::EXPIRATION );
		}


		return $data['count'];
	}

	public function get_web_form( $form_id ) {
		$transient = sprintf( '%s_%s', self::WEBFORM, $form_id );
		$data      = get_transient( $transient );

		if ( $data === false ) {
			$response = $this->smart_emailing->webForms()->getSingle( $form_id );

			if ( $response->getStatus() === 'error' ) {
				return false;
			}

			$data = $response->getData();

			set_transient( $transient, $data, self::EXPIRATION );
		}

		return $data;
	}
}
