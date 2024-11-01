<?php

namespace Smartemailing\Features;

use Smartemailing\Repositories\OrderRepository;
use Smartemailing\Repositories\SettingsRepository;
use Smartemailing\Settings\GeneralSettings;

class FrontendTracking {
	private OrderRepository $order_repository;
	private SettingsRepository $settings_repository;

	public function __construct(
		OrderRepository $order_repository,
		SettingsRepository $settings_repository
	) {
		$this->order_repository    = $order_repository;
		$this->settings_repository = $settings_repository;

		add_action( 'wp_head', array( $this, 'base_tracking_script' ) );
		add_action( 'woocommerce_cart_item_removed', array( $this, 'add_update_cart_flag' ) );
		add_filter( 'woocommerce_update_cart_action_cart_updated', array( $this, 'cart_updated' ), 1000 );
		add_action( 'woocommerce_add_to_cart', array( $this, 'add_update_cart_flag' ) );
		add_action( 'woocommerce_thankyou', array( $this, 'track_order' ) );
	}

	/**
	 * Tracking is disabled by cookie.
	 *
	 * @return bool
	 */
	public function is_disabled_by_cookie(): bool {
		$disable_tracking_by_cookie = $this->settings_repository->get_general_setting( GeneralSettings::DISABLE_TRACKING_BY_COOKIE );

		if ( empty( $disable_tracking_by_cookie ) || ! is_array( $disable_tracking_by_cookie ) ) {
			return false;
		}

		foreach ( $disable_tracking_by_cookie as $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}

			$rule_type    = $rule[ GeneralSettings::TYPE ] ?: null;
			$cookie_name  = $rule[ GeneralSettings::NAME ] ?: null;
			$cookie_value = $rule[ GeneralSettings::VALUE ] ?: null;

			if ( ! $rule_type || ! $cookie_name || ! $cookie_value ) {
				continue;
			}

			if ( $rule_type === 'has_value' && isset( $_COOKIE[ $cookie_name ] ) && $_COOKIE[ $cookie_name ] == $cookie_value ) {
				return true;
			} elseif ( $rule_type === 'not_have_value' && ! empty( $_COOKIE[ $cookie_name ] ) && $_COOKIE[ $cookie_name ] != $cookie_value ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Render base tracking script.
	 *
	 * @return void
	 */
	public function base_tracking_script(): void {
		if ( $this->is_disabled_by_cookie() || ! $this->settings_repository->get_general_setting( GeneralSettings::ENABLE_TRACKING ) || ! $this->settings_repository->get_general_setting( GeneralSettings::GUID ) ) {
			return;
		}
		?>
        <script type="text/javascript">
          (function (sm, a, rt, e, ma, il, i, ng) {
            a._se = a._se || [];
            for (ng = 0; ng < ma.length; ng++) {
              i = sm.createElement(rt);
              il = sm.getElementsByTagName(rt)[0];
              i.async = 1;
              i.src = e + ma[ng] + '.js';
              il.parentNode.insertBefore(i, il);
            }
          })
          (document, window, 'script', 'https://app.smartemailing.cz/js/tracking/', ['tracker']);
          _se.push(['init', '<?php echo esc_attr( $this->settings_repository->get_general_setting( GeneralSettings::GUID ) );?>']);
        </script>
		<?php
		$this->customer_identification();
		$this->page_visit();
		$this->track_cart();
	}

	/**
	 * Get customer data.
	 *
	 * @return array
	 */
	public function get_customer(): array {
		$data = [];
		if ( ! empty( WC()->customer ) ) {
			$data['emailaddress'] = WC()->customer->get_billing_email();
			$data['name']         = WC()->customer->get_billing_first_name();
			$data['surname']      = WC()->customer->get_billing_last_name();
			$data['street']       = WC()->customer->get_billing_address();
			$data['town']         = WC()->customer->get_billing_city();
			$data['postalcode']   = WC()->customer->get_billing_postcode();
			$data['country']      = WC()->customer->get_billing_country();
			$data['cellphone']    = WC()->customer->get_billing_phone();
		}

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();

			if ( empty( $data['emailaddress'] ) ) {
				$data['emailaddress'] = $user->user_email;
			}
			if ( empty( $data['name'] ) ) {
				$data['name'] = $user->first_name;
			}
			if ( empty( $data['surname'] ) ) {
				$data['surname'] = $user->last_name;
			}
		}

		return $data;
	}

	/**
	 * Track customer identification.
	 *
	 * @return void
	 */
	public function customer_identification(): void {
		$customer = $this->get_customer();
		if ( empty( $customer ) ) {
			return;
		}
		?>
        <script type="text/javascript">
          _se.push([
            'identify',
            {
              contact_data: <?php echo wp_json_encode( $this->get_customer() );?>,
              reidentify: false,
              update_existing: true
            }
          ]);
        </script>
	<?php }

	/**
	 * Track page visit.
	 *
	 * @return void
	 */
	public function page_visit(): void {
		$url        = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$attributes = [];

		if ( is_single( 'post' ) ) {
			$type       = 'article';
			$attributes = [ [ 'name' => 'post_id', 'value' => strval( get_the_ID() ) ] ];
		} elseif ( is_home() || is_front_page() ) {
			$type = 'home';
		} elseif ( is_cart() ) {
			$type = 'cart';
		} elseif ( is_product() ) {
			$type       = 'product';
			$attributes = [ [ 'name' => 'product_id', 'value' => strval( get_the_ID() ) ] ];
		} elseif ( is_product_category() ) {
			$type       = 'category';
			$attributes = [ [ 'name' => 'category_id', 'value' => strval( get_the_ID() ) ] ];
		} elseif ( is_search() ) {
			$type       = 'search';
			$attributes = [ [ 'name' => 'search_query', 'value' => get_search_query() ] ];
		} else {
			$type       = 'other';
			$attributes = [ [ 'name' => 'page_id', 'value' => strval( get_the_ID() ) ] ];
		}
		$data = [
			'url'        => sanitize_url( $url ),
			'visit_type' => $type,
		];
		if ( $attributes ) {
			$data['attributes'] = $attributes;
		}
		?>
        <script type="text/javascript">
          _se.push([
            'visit', <?php echo wp_json_encode( $data );?>
          ]);
        </script>
	<?php }

	/**
	 * Set the flag to update cart on next page load
	 */
	public function add_update_cart_flag(): void {
		if ( ! WC()->session ) {
			return;
		}

		WC()->session->set( 'se_update_cart', true );
	}

	/**
	 * Delete the flag to update cart on next page load
	 */
	public function delete_update_cart_flag(): void {
		if ( ! WC()->session ) {
			return;
		}

		WC()->session->set( 'se_update_cart', null );
	}

	/**
	 * Set update cart flag on updated cart
	 *
	 * @param $updated
	 */
	public function cart_updated( $updated ): void {
		if ( $updated ) {
			$this->add_update_cart_flag();
		}
	}

	/**
	 * Add cart data if requested - this is used for JS cart tracking.
	 *
	 * @return string|void
	 */
	public function track_cart() {
		if ( empty( WC()->session ) || ! WC()->session->get( 'se_update_cart' ) ) {
			return '';
		}
		if ( empty( WC()->cart ) ) {
			return '';
		}
		$this->delete_update_cart_flag();

		$items = array_values(
			array_map(
				function ( $item ) {
					/** @var \WC_Product $p */
					$p          = $item['data'];
					$product_id = $item['variation_id'] ?: $item['product_id'];

					$image_urls = array();
					if ( $p->get_image_id() ) {
						$image_urls[] = wp_get_attachment_image( $p->get_image_id(), 'full' );
					}
					foreach ( $p->get_gallery_image_ids() as $id ) {
						/** @var $ class_name */
						$image_urls[] = wp_get_attachment_image( $id, 'full' );
					}

					return array(
						'code'     => $p->get_id(),
						'name'     => $p->get_name(),
						'url'      => $p->get_permalink(),
						'price'    => [
							'with_vat'    => round( ( $item['line_total'] + $item['line_tax'] ) / $item['quantity'], wc_get_price_decimals() ),
							'without_vat' => round( ( $item['line_total'] ) / $item['quantity'], wc_get_price_decimals() ),
							'currency'    => get_woocommerce_currency(),
						],
						'quantity' => $item['quantity'],
					);
				},
				WC()->cart->get_cart()
			)
		);

		?>
        <script type="text/javascript">
          _se.push(['cart', {
            eshop_name: '<?php echo $this->settings_repository->get_general_setting( GeneralSettings::ESHOP_NAME )?>',
            items: <?php echo wp_json_encode( $items );?>
          }]);
        </script>
		<?php
	}

	/**
	 * Track order.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function track_order( int $order_id ): void {
		$order = $this->order_repository->get( $order_id );
		if ( ! $this->is_disabled_by_cookie() || ! $order ) {
			return;
		}
		$wc_order = $order->get_wc_order();
		if ( ! $wc_order ) {
			return;
		}
		$items = array_map( function ( $item ) use ( $wc_order ) {
			return [
				'code'     => $item->product->get_sku() ?: $item->product_id,
				'name'     => $item->name,
				'price'    => [
					'with_vat'    => $item->get_unit_price_tax_included(),
					'without_vat' => $item->get_unit_price_tax_excluded(),
					'currency'    => $wc_order->get_currency(),
				],
				'quantity' => $item->quantity,
			];
		}, $order->line_items );


		$data = [
			'eshop_name' => $this->settings_repository->get_general_setting( GeneralSettings::ESHOP_NAME ),
			'eshop_code' => $order->get_wc_order()->get_order_number(),
			'status'     => 'placed',
			'identify'   => [
				'contact_data'    => [
					'emailaddress' => $wc_order->get_billing_email(),
					'name'         => $wc_order->get_billing_first_name(),
					'surname'      => $wc_order->get_billing_last_name(),
					'street'       => $wc_order->get_billing_address_1(),
					'town'         => $wc_order->get_billing_city(),
					'postalcode'   => $wc_order->get_billing_postcode(),
					'country'      => $wc_order->get_billing_country(),
					'company'      => $wc_order->get_billing_company(),
					'cellphone'    => $wc_order->get_billing_phone(),
				],
				'reidentify'      => true,
				'update_existing' => true,


			],
			'items'      => $items,

		];
		?>
        <script type="text/javascript">
          _se.push(['order', <?php echo wp_json_encode( $data );?>]);
        </script>

	<?php }
}
