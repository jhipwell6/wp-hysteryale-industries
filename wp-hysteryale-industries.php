<?php

/**
 * Plugin Name: HysterYale Industries Integration
 * Plugin URI: https://hysteryale.com/
 * Description: WP HysterYale Industries Plugin
 * Version: 0.8
 * Author: WebFX
 * Author URI: https://webfx.com/
 * GitHub Plugin URI: jhipwell6/wp-hysteryale-industries
 * Primary Branch: main
 * Text Domain: wp-hyg-industries
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_HYG_Industries' ) ) :

	final class WP_HYG_Industries
	{
		/**
		 * @var string
		 */
		public $version = '0.8';

		/**
		 * @var string
		 */
		public $text_domain = 'wp-hyg-industries';

		/**
		 * Factory for returning industries
		 * @var null
		 */
		private $industry_factory = null;

		/**
		 * @var WP_HYG_Industries The single instance of the class
		 * @since 0.1
		 */
		protected static $instance = null;

		/**
		 * Main Instance
		 *
		 * Ensures only one instance is loaded or can be loaded.
		 *
		 * @since 0.1
		 * @static
		 * @return WP_HYG_Industries - Main instance
		 */
		public static function instance()
		{
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct()
		{
			$this->define_constants();

			/**
			 * Once plugins are loaded, initialize
			 */
			add_action( 'plugins_loaded', [ $this, 'setup' ], -15 );
		}

		/**
		 * Define WC Constants
		 */
		private function define_constants()
		{
			global $wpdb;
			$upload_dir = wp_upload_dir();
			$this->define( 'WP_HYG_INDUSTRIES_PLUGIN_FILE', __FILE__ );
			$this->define( 'WP_HYG_INDUSTRIES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'WP_HYG_INDUSTRIES_TEXT_DOMAIN', $this->text_domain );
			$this->define( 'WP_HYG_INDUSTRIES_VERSION', $this->version );
		}

		/**
		 * Setup needed includes and actions for plugin
		 * @hooked plugins_loaded -15
		 */
		public function setup()
		{
			$this->includes();
			$this->init_factories();
		}

		/**
		 * Define constant if not already set
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value )
		{
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 * string $type ajax, frontend or admin
		 * @return bool
		 */
		public function is_request( $type )
		{
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Include required files used in admin and on the frontend.
		 */
		public function includes()
		{			
			// Models
			// WP_HYG_Industries\Models\Abstracts\Post_Model
			include_once $this->plugin_path() . '/includes/models/abstracts/Post_Model.php';
			// WP_HYG_Industries\Models\Industry
			include_once $this->plugin_path() . '/includes/models/Industry.php';

			// Core
			include_once $this->plugin_path() . '/includes/core/general-functions.php';
			include_once $this->plugin_path() . '/includes/core/helpers.php';
			include_once $this->plugin_path() . '/includes/core/media.php';
			include_once $this->plugin_path() . '/includes/core/post-types.php';
			include_once $this->plugin_path() . '/includes/core/custom-fields.php';
			include_once $this->plugin_path() . '/includes/core/form-handler.php';
			include_once $this->plugin_path() . '/includes/core/abstracts/factory.php';
			include_once $this->plugin_path() . '/includes/core/industry-factory.php';

			// IMPORTANT - Controllers must be included after Models.
			// This is because cron actions hooked/defined in Controllers will fire as soon as the Controller is included and the add_action() with cron hook name is called.
			// If the Controller uses a Model in the Cron action callback, that Model will NOT have been included yet.
			// Controllers
			include_once $this->plugin_path() . '/includes/controllers/hyg-industries-template.php';
			include_once $this->plugin_path() . '/includes/controllers/hyg-industries-industry.php';
			
			if ( $this->is_request( 'admin' ) ) {
				include_once $this->plugin_path() . '/includes/controllers/admin/hyg-industries-industry-admin.php';
			}
		}

		/**
		 * Create factories to create new class instances
		 */
		public function init_factories()
		{
			$this->industry_factory = new \WP_HYG_Industries\Core\Industry_Factory;
		}
		
		/**
		 * Return the Model of a industry item
		 * @param  mixed $industry_item item
		 */
		public function Industry( $industry_item = false )
		{
			return $this->industry_factory->get( $industry_item );
		}

		/**
		 * Get helpers instance.
		 *
		 * @return Helpers
		 */
		public function Helpers()
		{
			return \WP_HYG_Industries\Core\Helpers::instance();
		}

		/**
		 * Get media instance.
		 *
		 * @return Helpers
		 */
		public function Media()
		{
			return \WP_HYG_Industries\Core\Media::instance();
		}
		
		/**
		 * Load the view
		 */
		public function view( $template, $data = [] )
		{
			if ( ! empty( $data ) ) {
				extract( $data );
			}

			ob_start();
			include $this->plugin_path() . '/includes/views/' . $template . '.php';
			return ob_get_clean();
		}

		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url()
		{
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path()
		{
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get the log path.
		 * @return string
		 */
		public function log_path()
		{
			return $this->plugin_path() . '/logs';
		}

		/**
		 * Get Ajax URL.
		 * @return string
		 */
		public function ajax_url()
		{
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		/**
		 * log information to the debug log
		 * @param  string|array $log [description]
		 * @return void
		 */
		public function debug_log()
		{
			$log_location = $this->log_path() . '/wp-hyg-industries-debug.log';
			$args = func_get_args();
			$log = $this->log( $args );
			error_log( $log, 3, $log_location );
		}

		public function inspect()
		{
			$args = func_get_args();
			$log = $this->log( $args );
			echo '<pre>';
			echo $log;
			echo '</pre>';
		}

		private function log( $args )
		{
			$datetime = new \DateTime( 'NOW' );
			$timestamp = $datetime->format( 'Y-m-d H:i:s' );
			$formatted = array_map( function ( $item ) {
				return print_r( $item, true );
			}, $args );
			array_unshift( $formatted, $timestamp );
			return implode( ' ', $formatted ) . "\n";
		}

	}

	endif;

/**
 * Returns the main instance of WP_HYG_Industries to prevent the need to use globals.
 *
 * @since  0.1
 * @return WP_HYG_Industries
 */
function WP_HYG_Industries()
{
	return WP_HYG_Industries::instance();
}

WP_HYG_Industries();
