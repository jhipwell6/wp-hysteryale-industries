<?php

namespace WP_HYG_Industries\Core;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Media
{
	protected static $instance = null;

	public function __construct()
	{
		add_filter( 'image_sideload_extensions', array( $this, 'add_allowed_extensions' ), 10, 2 );
		return $this;
	}

	public static function instance()
	{
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function add_allowed_extensions( $allowed_extensions, $file )
	{
		$allowed_extensions[] = 'pdf';
		$allowed_extensions[] = 'zip';
		return $allowed_extensions;
	}

	public function sideload_image( $file, $post_id = 0, $desc = null, $return_type = 'html' )
	{
		if ( ! function_exists( 'media_sideload_image' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
		}
		
		return media_sideload_image( $file, $post_id, $desc, $return_type );
	}

	public function get_image_from_library_by_url( $image_url, $target_dir = false, $bundle_type = 'images' )
	{
		$image_name = $this->get_filename_from_url( $image_url );
		return $this->get_image_from_library( $image_name, $target_dir, $bundle_type );
	}

	public function get_image_from_library( $image_name, $target_dir = false, $bundle_type = 'images' )
	{
		global $wpdb;

		$original_image_name = $image_name;

		if ( ! $target_dir ) {
			$wp_uploads = wp_upload_dir();
			$target_dir = $wp_uploads['path'];
		}

		// Prepare scaled image file name.
		$scaled_name = $image_name;
		$rotated_name = $image_name;
		$ext = $this->get_file_extension( $image_name );
		if ( $ext ) {
			$scaled_name = str_replace( '.' . $ext, '-scaled.' . $ext, $image_name );
			$rotated_name = str_replace( '.' . $ext, '-rotated.' . $ext, $image_name );
		}

		$attch = '';

		// search attachment by attached file
		$attachment_metas = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key = %s AND (meta_value = %s OR meta_value = %s OR meta_value = %s OR meta_value LIKE %s ESCAPE '$' OR meta_value LIKE %s ESCAPE '$' OR meta_value LIKE %s ESCAPE '$');", '_wp_attached_file', $image_name, $scaled_name, $rotated_name, "%/" . str_replace( '_', '$_', $image_name ), "%/" . str_replace( '_', '$_', $scaled_name ), "%/" . str_replace( '_', '$_', $rotated_name ) ) );

		if ( ! empty( $attachment_metas ) ) {
			foreach ( $attachment_metas as $attachment_meta ) {
				$attch = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->posts . " WHERE ID = %d;", $attachment_meta->post_id ) );
				if ( ! empty( $attch ) ) {
					break;
				}
			}
		}

		if ( empty( $attch ) ) {
			$attachment_metas = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key = %s AND (meta_value = %s OR meta_value = %s OR meta_value = %s OR meta_value LIKE %s ESCAPE '$' OR meta_value LIKE %s ESCAPE '$' OR meta_value LIKE %s ESCAPE '$');", '_wp_attached_file', sanitize_file_name( $image_name ), sanitize_file_name( $scaled_name ), sanitize_file_name( $rotated_name ), "%/" . str_replace( '_', '$_', sanitize_file_name( $image_name ) ), "%/" . str_replace( '_', '$_', sanitize_file_name( $scaled_name ) ), "%/" . str_replace( '_', '$_', sanitize_file_name( $rotated_name ) ) ) );

			if ( ! empty( $attachment_metas ) ) {
				foreach ( $attachment_metas as $attachment_meta ) {
					$attch = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->posts . " WHERE ID = %d;", $attachment_meta->post_id ) );
					if ( ! empty( $attch ) ) {
						break;
					}
				}
			}
		}

		if ( empty( $attch ) ) {
			// Search attachment by file name with extension.
			$attch = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->posts . " WHERE (post_title = %s OR post_name = %s) AND post_type = %s", $image_name, sanitize_title( $image_name ), "attachment" ) . " AND post_mime_type LIKE 'image%';" );
		}

		// Search attachment by file headers.
		if ( empty( $attch ) && @file_exists( $target_dir . DIRECTORY_SEPARATOR . $original_image_name ) ) {
			if ( ! function_exists( 'wp_read_image_metadata' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
			}
			if ( $bundle_type == 'images' && ( $img_meta = wp_read_image_metadata( $target_dir . DIRECTORY_SEPARATOR . $original_image_name ) ) ) {
				if ( trim( $img_meta['title'] ) && ! is_numeric( sanitize_title( $img_meta['title'] ) ) ) {
					$img_title = $img_meta['title'];
					$attch = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = %s AND post_mime_type LIKE %s;", $img_title, "attachment", "image%" ) );
				}
			}
		}

		return $attch;
	}

	public function get_file_extension( $str )
	{
		$i = strrpos( $str, '.' );
		if ( ! $i )
			return '';
		$l = strlen( $str ) - $i;
		$ext = substr( $str, $i + 1, $l );
		return ( strlen( $ext ) <= 4 ) ? $ext : '';
	}

	public function get_filename_from_url( $file_url )
	{
		$parts = wp_parse_url( $file_url );
		if ( isset( $parts['path'] ) ) {
			return basename( $parts['path'] );
		}
	}

}
