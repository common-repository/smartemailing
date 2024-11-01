<?php

namespace Smartemailing;

use Smartemailing\Integrations\SmartEmailingApi;
use Smartemailing\Repositories\SettingsRepository;

class Templates {
	const RENDER_WORDPRESS = 'wordpress';

	const RENDER_SMARTEMAILING = 'smartemailing';

	const WEBFORM_LINK = 'https://app.smartemailing.cz/public/web-forms-v2/subscribe/';

	public function __construct(
		private SmartEmailingApi $smart_emailing_api,
		private SettingsRepository $settings_repository
	) {
	}

	public function render_downloader( $args ) {
		if ( isset( $args['contactlist_id'] ) ) {
			$contact_list_id = (int) $args['contactlist_id'];

			if ( empty( $contact_list_id ) ) {
				return '';
			}

			$count = $this->smart_emailing_api->get_download_count( $contact_list_id );
			if ( false === $count ) {
				return 'Contact list not found';
			}

			$title = '';
			if ( ! empty( $args['downloader_title'] ) ) {
				$title = '<h2>' . $args['downloader_title'] . '</h2>';
			}

			$beforeText = '';
			if ( ! empty( $args['downloader_before_text'] ) ) {
				$beforeText = $args['downloader_before_text'];
			}

			return '
				<div class="smartemailing-downloader-list" style="' . ( ! empty( $args['downloader_color'] ) ? 'color: ' . $args['downloader_color'] : '' ) . '">
					' . $title . '
					<div class="smartemailing-downloader-content" style="' . ( ! empty( $args['font_size'] ) ? 'font-size:' . $args['font_size'] . 'px' : '' ) . '">
						 ' . $beforeText . $count . '
					</div>
				</div>
				';
		}
	}

	public function render_web_form( $attributes = [], $content = '', $wfId = null ) {
		if ( isset( $attributes['webform_id'] ) ) {
			$web_form_id = (int) $attributes['webform_id'];

			if ( empty( $web_form_id ) ) {
				return '';
			}


			$web_form = $this->smart_emailing_api->get_web_form( $web_form_id );

			if ( ! $web_form ) {
				return 'WebForm nenalezen';
			}

			$title = '';
			if ( isset( $attributes['webform_title'] ) && ! empty( $attributes['webform_title'] ) ) {
				$title = '<h2 style="' . ( ! empty( $attributes['webform_color'] ) ? 'color: ' . $attributes['webform_color'] : '' ) . '">' . $attributes['webform_title'] . '</h2>';
			}

			$submit = 'ODESLAT';
			if ( isset( $attributes['webform_submit_text'] ) && ! empty( $attributes['webform_submit_text'] ) ) {
				$submit = $attributes['webform_submit_text'];
			}

			$hash = $this->get_hash_from_link(
				$web_form['form_action']
			);

			if ( $this->settings_repository === self::RENDER_WORDPRESS ) {
				return $this->get_webform_html(
					$web_form,
					$hash,
					$title,
					$submit,
					$attributes
				);
			}

			return '
				<div class="smartemailing-webform-list">
					' . $title . '
					<script src="' . self::WEBFORM_LINK . $hash . '" id="se-webformScriptLoader-' . $hash . '" async defer></script>
				</div>
				';
		}
	}

	public function get_hash_from_link( $link ) {
		$link = explode( '/', $link );

		return explode( '?', $link[ count( $link ) - 1 ] )[0];
	}

	public function get_webform_html( $webForm, $hash, $title, $submit, $attributes ) {
		$hash = explode( '/', $webForm['form_action'] );
		$hash = str_replace( '?posted=1', '', $hash[ count( $hash ) - 1 ] );

		$actionUrl = 'https://app.smartemailing.cz/public/web-forms-v2/display-form/' . $hash;

		$form = $title . '<form action="' . $actionUrl . '" class="smartemailing_webforms smartemailing_webform_' . $webForm['id'] . '" method="post"  id="se20-webform-' . $hash . '">';

		foreach ( $webForm['structure'] as $structure ) {
			$form .= '
				<div class="label_' . $structure->html_input_name . '">
					<label for="' . $structure->html_input_name . '" style="' . ( ! empty( $attributes['webform_color'] ) ? 'color: ' . $attributes['webform_color'] : '' ) . '">' . $structure->label . ' <strong>' . ( (bool) $structure->is_required ? '*' : '' ) . '</strong></label>
				</div>
				<div class="input_' . $structure->html_input_name . '">
					<input style="width: 100%" type="' . $structure->html_input_type . '" name="fields[' . $structure->html_input_name . ']" value="">
				</div>
			';
		}

		$form .= '
				<div>
					<input type="submit" name="_submit" value="' . strip_tags( $submit ) . '">
					<input type="hidden" name="referrer" id="se-ref-field-id" value="">
					<input type="hidden" name="sessionid" id="se-sessionid-field" value="">
					<input type="hidden" name="sessionUid" id="se-sessionUid-field" value="">
					<input type="hidden" name="_do" value="webFormHtmlRenderer-webFormForm-submit">
				</div>

				</form>
			';

		return $form;
	}

}
