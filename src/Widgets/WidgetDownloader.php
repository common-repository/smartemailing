<?php

namespace Smartemailing\Widgets;

use Smartemailing\Integrations\SmartEmailingApi;
use Smartemailing\Templates;

class WidgetDownloader extends \WP_Widget {
	private $smart_emailing_api;
	private $templates;

	function __construct() {
		parent::__construct(
				'Smartemailing_Widget_Downloader',
				__( 'SmartEmailing - Download Count', 'smartemailing' ),
				[
						'description' => __( 'Download count of your list', 'smartemailing' ),
				]
		);

		$this->smart_emailing_api = smartemailing_container()->get( SmartEmailingApi::class );
		$this->templates          = smartemailing_container()->get( Templates::class );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', ! empty( $instance['title'] ) ? $instance['title'] : '' );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$args = [
				'contactlist_id'         => isset( $instance['contactlist_id'] ) ? $instance['contactlist_id'] : '',
				'downloader_title'       => isset( $instance['downloader_title'] ) ? $instance['downloader_title'] : '',
				'downloader_before_text' => isset( $instance['downloader_before_text'] ) ? $instance['downloader_before_text'] : '',
				'downloader_color'       => isset( $instance['downloader_color'] ) ? $instance['downloader_color'] : '',
		];

		echo $this->templates->render_downloader( $args );

		echo isset( $args['after_widget'] ) ? $args['after_widget'] : '';
	}

	public function form( $instance ) { ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'downloader_title' ); ?>">
				<?php _e( 'Title:' ); ?>
			</label>

			<input class="widefat"
				   id="<?php echo $this->get_field_id( 'downloader_title' ); ?>"
				   name="<?php echo $this->get_field_name( 'downloader_title' ); ?>"
				   type="text"
				   value="<?php echo esc_attr( $instance['downloader_title'] ); ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'downloader_before_text' ); ?>">
				<?php _e( 'Text před počtem:' ); ?>
			</label>

			<input class="widefat"
				   id="<?php echo $this->get_field_id( 'downloader_before_text' ); ?>"
				   name="<?php echo $this->get_field_name( 'downloader_before_text' ); ?>"
				   type="text"
				   value="<?php echo esc_attr( $instance['downloader_before_text'] ); ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'contactlist_id' ); ?>">
				<?php _e( 'Contact list:' ); ?>
			</label>

			<select name="<?php echo $this->get_field_name( 'contactlist_id' ); ?>">
				<?php foreach ( $this->smart_emailing_api->get_lists_options() as $contactList ) { ?>
					<option value="<?php echo $contactList['value'] ?>" <?php echo( isset( $instance['contactlist_id'] ) && esc_attr( $instance['contactlist_id'] ) == $contactList['value'] ? 'selected' : '' ) ?>>#<?php echo $contactList['value'] ?> - <?php echo $contactList['label'] ?></option>
				<?php } ?>
			</select>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance                           = [];
		$instance['downloader_title']       = ( ! empty( $new_instance['downloader_title'] ) ) ? strip_tags( $new_instance['downloader_title'] ) : '';
		$instance['downloader_before_text'] = ( ! empty( $new_instance['downloader_before_text'] ) ) ? strip_tags( $new_instance['downloader_before_text'] ) : '';
		$instance['contactlist_id']         = ( ! empty( $new_instance['contactlist_id'] ) ) ? strip_tags( $new_instance['contactlist_id'] ) : '';

		return $instance;
	}
}
