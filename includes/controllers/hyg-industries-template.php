<?php

namespace WP_HYG_Industries\Controllers;

if ( ! defined( 'ABSPATH' ) )
	exit;

class HYG_Industries_Template
{
	protected static $instance;

	public function __construct()
	{
		add_filter( 'template_include', [ $this, 'template_loader' ] );
	}

	public static function instance()
	{
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function template_loader( $template )
	{
		if ( ! is_singular( 'industry' ) ) {
			return $template;
		}
		
		$theme_file = get_stylesheet_directory() . 'single-industry.php';
		if ( file_exists( $theme_file ) ) {
			return $theme_file;
		} else {
			return WP_HYG_Industries()->plugin_path() . '/includes/views/single-industry.php';
		}
		
		return $template;
	}
}

HYG_Industries_Template::instance();
