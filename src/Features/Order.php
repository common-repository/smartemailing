<?php

namespace Smartemailing\Features;

use Smartemailing\Integrations\SmartEmailingApi;
use Smartemailing\Repositories\SettingsRepository;
use Smartemailing\Settings\GeneralSettings;
use WC_Order;

class Order {
	const OPT_IN_KEY = 'smartemailing_opt_in';
	const ORDER_META_AGREED_KEY = '_smartemailing_agreed';

	private SmartEmailingApi $smart_emailing_api;
	private SettingsRepository $settings_repository;

	public function __construct(
		SmartEmailingApi $smart_emailing_api,
		SettingsRepository $settings_repository
	) {
		$this->smart_emailing_api  = $smart_emailing_api;
		$this->settings_repository = $settings_repository;

		add_action( 'woocommerce_checkout_after_terms_and_conditions', array( $this, 'render_opt_in' ) );
		add_action( 'woocommerce_checkout_order_created', array( $this, 'order_created' ) );
		add_action( 'woocommerce_order_status_changed', array(
			$this,
			'maybe_schedule_subscribe_to_smartemailing'
		), 10, 3 );
		add_action( 'smartemailing_subscribe', array( $this, 'subscribe_to_smartemailing' ), 10, 3 );
	}

	/**
	 * Render opt-in checkbox in checkout.
	 *
	 * @return void
	 */
	public function render_opt_in(): void {
		if ( ! $this->settings_repository->get_general_setting( GeneralSettings::SHOW_CHECKBOX ) ) {
			return;
		}
		?>
        <p class="form-row smartemailing-opt_in">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
                       name="<?php echo self::OPT_IN_KEY; ?>" style="width: auto;"
					<?php checked( isset( $_POST[ self::OPT_IN_KEY ] ) || $this->settings_repository->get_general_setting( GeneralSettings::CHECKBOX_CHECKED ), true ); // WPCS: input var ok, csrf ok.?>
                />
                <span class="smartemailing-opt_in-checkbox-text"><?php echo sanitize_text_field( $this->settings_repository->get_general_setting( GeneralSettings::SUBSCRIPTION_TEXT ) ); ?></span>&nbsp;
            </label>
        </p>
		<?php
	}

	/**
	 * Save agreed meta to order.
	 *
	 * @param WC_Order|int $order Order.
	 *
	 * @return void
	 */
	public function order_created( WC_Order|int $order ): void {
		if ( $this->settings_repository->get_general_setting( GeneralSettings::SHOW_CHECKBOX ) && empty( $_POST[ self::OPT_IN_KEY ] ) ) {
			return;
		}

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order ) {
			return;
		}

		$order->update_meta_data( self::ORDER_META_AGREED_KEY, 1 );
		$order->save();
	}

	/**
	 * Maybe schedule subscribe to SmartEmailing.
	 *
	 * @param int $order_id Order ID.
	 * @param string|null $old_status Old status.
	 * @param string|null $new_status New status.
	 *
	 * @return void
	 */
	public function maybe_schedule_subscribe_to_smartemailing( int $order_id, ?string $old_status = '', ?string $new_status = '' ): void {
		$order = wc_get_order( $order_id );
		if ( ! $order->get_meta( self::ORDER_META_AGREED_KEY ) ) {
			return;
		}

		$list_ids = $this->smart_emailing_api->get_order_list_ids( $order, array(), (string) $new_status );

		if ( empty( $list_ids ) ) {
			return;
		}

		as_schedule_single_action(
			time(),
			'smartemailing_subscribe',
			array(
				'order_id' => $order_id,
				'list_ids' => $list_ids,
			),
			'smartemailing'
		);
	}

	/**
	 * Subscribe customer to SmartEmailing.
	 *
	 * @param int $order_id Order ID.
	 * @param array $list_ids List IDs.
	 *
	 * @return void
	 */
	public function subscribe_to_smartemailing( int $order_id, array $list_ids ): void {
		$order = wc_get_order( $order_id );

		$this->smart_emailing_api->subscribe( $this->smart_emailing_api->get_customer_data_from_order( $order, $list_ids ) );
		$smart_emailing_order = $this->smart_emailing_api->get_order( $order );
		if ( $smart_emailing_order ) {
			$this->smart_emailing_api->send_order( $smart_emailing_order );
		}
	}
}
