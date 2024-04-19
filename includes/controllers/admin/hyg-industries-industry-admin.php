<?php

namespace WP_HYG_Industries\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) )
	exit;

class HYG_Industries_Industry_Admin
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

	private function is_this_post_type_screen()
	{
		global $pagenow;
		return ( 'edit.php' == $pagenow && isset( $_GET['post_type'] ) && 'industry' == $_GET['post_type'] ) ? true : false;
	}
}

HYG_Industries_Industry_Admin::instance();