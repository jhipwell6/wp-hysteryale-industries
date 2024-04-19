<?php

namespace WP_HYG_Industries\Controllers;

if ( ! defined( 'ABSPATH' ) )
	exit;

class HYG_Industries_Industry
{
	protected static $instance;

	public function __construct()
	{
		
	}

	public static function instance()
	{
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

HYG_Industries_Industry::instance();