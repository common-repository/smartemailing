<?php

namespace Smartemailing\Widgets;

use Smartemailing\Integrations\SmartEmailingApi;
use Smartemailing\Templates;

class WidgetWebform extends \WP_Widget {
	private $smart_emailing_api;
	private $templates;

	function __construct() {
		parent::__construct(
				'Smartemailing_Widget_WebForm',
				__( 'SmartEmailing - Web form', 'smartemailing' ),
				[
						'description' => __( 'Embed web form', 'smartemailing' ),
				]
		);
		$this->smart_emailing_api = smartemailing_container()->get( SmartEmailingApi::class );
		$this->templates          = smartemailing_container()->get( Templates::class );
	}

	public function widget( $args, $instance ) {
		$wfHtml = $this->templates->render_web_form( [
				'webform_id' => $instance['webform_id'],
		] );

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo $wfHtml;

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Web form', 'smartemailing' );
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title:' ); ?>
			</label>

			<input class="widefat"
				   id="<?php echo $this->get_field_id( 'title' ); ?>"
				   name="<?php echo $this->get_field_name( 'title' ); ?>"
				   type="text"
				   value="<?php echo esc_attr( $title ); ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'webform_id' ); ?>">
				<?php _e( 'Web form:' ); ?>
			</label>
			<select name="<?php echo $this->get_field_name( 'webform_id' ); ?>">
				<?php foreach ( $this->smart_emailing_api->get_webforms_options() as $webform ) { ?>
					<option value="<?php echo $webform['value'] ?>" <?php echo( isset( $instance['webform_id'] ) && esc_attr( $instance['webform_id'] ) == $webform['value'] ? 'selected' : '' ) ?>>#<?php echo $webform['value'] ?> - <?php echo $webform['label'] ?></option>
				<?php } ?>
			</select>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance               = [];
		$instance['title']      = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['webform_id'] = ( ! empty( $new_instance['webform_id'] ) ) ? strip_tags( $new_instance['webform_id'] ) : '';

		return $instance;
	}
}
