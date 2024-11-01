<?php

namespace Smartemailing\Blocks;


use Smartemailing\Integrations\SmartEmailingApi;
use Smartemailing\Templates;
use SmartemailingDeps\Wpify\CustomFields\CustomFields;

class DownloaderBlock {
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
				'name'            => 'smartemailing/block-downloader',
				'title'           => __( 'SmartEmailing Downloader', 'smartemailing' ),
				'icon'            => 'info',
				'category'        => 'smartemailing',
				'render_callback' => array( $this, 'render' ),
				'items'           => array(
					array(
						'type'  => 'text',
						'id'    => 'downloader_title',
						'title' => __( 'Title', 'smartemailing' ),
					),
					array(
						'type'    => 'select',
						'id'      => 'contactlist_id',
						'options' => function () {
							return $this->api->get_lists_options();
						},
						'title'   => __( 'Contact list', 'smartemailing' ),
					),
				),
			)
		);
	}

	public function render( array $block_attributes ) {
		if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return sprintf( '<h2>Download count %s </h2>', $block_attributes['contactlist_id'] ?? '' );
		}

		return $this->templates->render_downloader( $block_attributes );
	}
}
