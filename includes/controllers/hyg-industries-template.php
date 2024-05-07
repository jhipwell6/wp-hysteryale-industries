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
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ], 9999 );
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
	
	public function enqueue_frontend_scripts()
	{		
		wp_enqueue_style(
			WP_HYG_INDUSTRIES_TEXT_DOMAIN . '-styles',
			WP_HYG_Industries()->plugin_url() . '/assets/css/wp-hyg-industries-frontend.css',
			array(),
			filemtime( WP_HYG_Industries()->plugin_path() . '/assets/css/wp-hyg-industries-frontend.css' ),
		);
	}
}

HYG_Industries_Template::instance();
