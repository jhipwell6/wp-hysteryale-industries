<?php

namespace WP_HYG_Industries\Core;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Helpers
{
	protected static $instance = null;

	public function __construct()
	{
		return $this;
	}

	public static function instance()
	{
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get_current_post_type()
	{
		global $post, $typenow, $current_screen;

		if ( $post && $post->post_type )
			return $post->post_type;

		elseif ( $typenow )
			return $typenow;

		elseif ( $current_screen && $current_screen->post_type )
			return $current_screen->post_type;

		elseif ( isset( $_REQUEST['post_type'] ) )
			return sanitize_key( $_REQUEST['post_type'] );

		return null;
	}
	
	public function is_fl_builder_loop( $id, &$query )
	{
		if ( ! is_admin() && isset( $query->query_vars['fl_builder_loop'] ) && $query->query['settings']->id == $id ) {
			return true;
		}
		
		return false;
	}
	
	public function maybe_explode( $value, $delimiter = '|' )
	{
		return strpos( $value, $delimiter ) !== false ? explode( $delimiter, $value ) : (array) $value;
	}

}
