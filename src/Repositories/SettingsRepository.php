<?php

namespace Smartemailing\Repositories;

use Smartemailing\Settings;

class SettingsRepository {

	/**
	 * @var array
	 */
	public array $options = array();

	/**
	 * Get option.
	 *
	 * @param $section
	 * @param string $key
	 * @param null $default
	 *
	 * @return mixed
	 */
	public function get_option( $section, string $key = '', $default = null ): mixed {
		if ( empty( $this->options[ $section ] ) ) {
			$this->get_options( $section );
		}

		if ( isset( $this->options[ $section ][ $key ] ) ) {
			return $this->options[ $section ][ $key ];
		}

		return $default;
	}

	/**
	 * Get all options
	 *
	 * @param string $section Section ID.
	 *
	 * @return mixed
	 */
	public function get_options( string $section ): mixed {
		if ( empty( $this->options[ $section ] ) ) {
			$this->options[ $section ] = get_option( $section, array() );
		}

		return $this->options[ $section ];
	}

	/**
	 * Get all general settings.
	 *
	 * @return mixed
	 */
	public function get_general_settings(): mixed {
		return $this->get_options( Settings::SECTION_GENERAL );
	}

	/**
	 * Get option form general settings.
	 *
	 * @param string $id ID.
	 * @param mixed $default Default value.
	 *
	 * @return mixed
	 */
	public function get_general_setting( string $id, mixed $default = null ): mixed {
		return $this->get_option( Settings::SECTION_GENERAL, $id, $default );
	}

	/**
	 * Get settings url
	 *
	 * @return string
	 */
	public function get_settings_url(): string {
		return add_query_arg( [ 'section' => Settings::SECTION_GENERAL ], admin_url( 'admin.php?page=wc-settings&tab=smartemailing' ) );
	}

}
