<?php

namespace Smartemailing\PostTypes;

use Smartemailing\Integrations\SmartEmailingApi;
use SmartemailingDeps\Wpify\CustomFields\CustomFields;

class ProductPostType {
	const SMARTEMAILING_LISTS = 'smartemaling_lists';
	const LISTS = 'lists';
	const STATUSES = 'statuses';

	private CustomFields $custom_fields;
	private SmartEmailingApi $smart_emailing_api;

	public function __construct(
		CustomFields $custom_fields,
		SmartEmailingApi $smart_emailing_api

	) {
		$this->custom_fields = $custom_fields;
		$this->smart_emailing_api = $smart_emailing_api;
		$this->setup();
	}

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup(): void {
		$this->custom_fields->create_product_options(
			array(
				'tab'   => array(
					'id'       => 'woo_se',
					'label'    => 'SmartEmailing',
					'priority' => 1,
					'class'    => array(),
				),
				'items' => array(
					array(
						'id'    => self::SMARTEMAILING_LISTS,
						'label' => __( 'Lists', 'smartemailing' ),
						'type'  => 'multi_group',
						'items' => array(
							array(
								'type'    => 'multi_select',
								'id'      => self::LISTS,
								'label'   => __( 'Add to lists', 'smartemailing' ),
								'options' => function () {
									return $this->smart_emailing_api->get_lists_options();
								},
							),
							array(
								'type'    => 'multi_select',
								'id'      => self::STATUSES,
								'label'   => __( 'Statuses', 'smartemailing' ),
								'options' => function () {
									$statuses = array();
									foreach ( wc_get_order_statuses() as $key => $status ) {
										$statuses[] = array(
											'label' => $status,
											'value' => $key,
										);
									}

									return $statuses;
								},
							),
						),
					),
				),
			)
		);
	}
}
