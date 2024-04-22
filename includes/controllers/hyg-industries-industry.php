<?php

namespace WP_HYG_Industries\Controllers;

if ( ! defined( 'ABSPATH' ) )
	exit;

class HYG_Industries_Industry
{
	protected static $instance;

	public function __construct()
	{
		add_shortcode( 'hyg_industry_equipment_card', [ $this, 'industry_equipment_card_shortcode' ], 10, 1 );
	}

	public static function instance()
	{
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function industry_equipment_card_shortcode()
	{
		if ( function_exists( 'HYSTERYALE_UPDATER' ) ) {
			$Equipment = HYSTERYALE_UPDATER()->equipment();
			return WP_HYG_Industries()->view( 'equipment-card', [ 'Equipment' => $Equipment ] );
		}
		return '';
	}

}

HYG_Industries_Industry::instance();
