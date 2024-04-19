<?php

namespace WP_HYG_Industries\Core;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Custom_Fields
{
	protected static $instance;

	/**
	 * Initializes variables and sets up WordPress hooks/actions.
	 * @return void
	 */
	protected function __construct()
	{
		add_action( 'acf/init', array( $this, 'add_fields' ), 10 );
	}

	/**
	 * Static Singleton Factory Method
	 * @return self
	 */
	public static function instance()
	{
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function add_fields()
	{
		$fields_path = WP_HYG_Industries()->plugin_path() . '/includes/core/acf-fields/';
		$files =  glob( $fields_path . '*.php' );
		if ( ! empty( $files ) ) {
			foreach ( $files as $field ) {
				include_once( $field );
			}
		}
	}

}

Custom_Fields::instance();
