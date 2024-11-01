<?php

namespace Smartemailing\Blocks;


use Smartemailing\Integrations\SmartEmailingApi;
use Smartemailing\Templates;
use SmartemailingDeps\Wpify\CustomFields\CustomFields;

class WebformBlock {
	private CustomFields $wcf;
	private SmartEmailingApi $api;
	private Templates $templates;

	public function __construct(
		CustomFields $wcf,
		SmartEmailingApi $api,
		Templates $templates
	) {
		$this->wcf       = $wcf;
		$this->api       = $api;
		$this->templates = $templates;
		$this->wcf->create_gutenberg_block(
			array(
				'name'            => 'smartemailing/block-editable',
				'title'           => __( 'SmartEmailing Webform', 'smartemailing' ),
				'icon'            => 'info',
				'category'        => 'smartemailing',
				'render_callback' => array( $this, 'render' ),
				'items'           => array(
					array(
						'type'  => 'text',
						'id'    => 'title',
						'title' => __( 'Title', 'smartemailing' ),
					),
					array(
						'type'    => 'select',
						'id'      => 'webform_id',
						'options' => function () {
							return $this->api->get_webforms_options( false );
						},
						'title'   => __( 'Form', 'smartemailing' ),
					),
				),
			)
		);
	}

	public function render( array $block_attributes ) {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return sprintf( '<h2>Webform %s </h2>', $block_attributes['webform_id'] ?? '' );
		}

		return $this->templates->render_web_form( $block_attributes );
	}
}
