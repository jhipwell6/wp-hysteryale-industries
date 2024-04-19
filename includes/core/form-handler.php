<?php

namespace WP_HYG_Industries\Core;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Form_Handler
{

	static public function pre_validate_form( $nonce_name, $action, $form_data )
	{
		if ( self::filter_input( 'action' ) !== $action ) {
			$error = new \WP_Error( 'invalid_action', 'Something went wrong. Invalid form action.' );
			wp_send_json_error( $error, 500 );
			die();
		}

		if ( ! self::is_nonce_verified( $nonce_name, $action, $form_data ) ) {
			$error = new \WP_Error( 'invalid_nonce', 'Unauthorized. Invalid nonce.' );
			wp_send_json_error( $error, 403 );
			die();
		}
	}

	static public function get_form_data( $post_var )
	{
		parse_str( self::filter_input( $post_var ), $form_data );
		if ( is_null( $form_data ) ) {
			$form_data = self::filter_input( $post_var );
		}
		return $form_data;
	}

	static public function merge_array_data( $data )
	{
		$new_data = array();
		foreach ( $data as $key => $arr ) {
			if ( is_array( $arr ) && ! empty( $arr ) ) {
				$parts = explode( '/', $key );
				$i = 0;
				foreach ( $arr as $key => $value ) {
					$new_data[$parts[0]][$i][$parts[1]] = $value;
					$i ++;
				}
			}
		}
		return $new_data;
	}

	static public function validate_form_data( $form_data )
	{
		if ( empty( $form_data ) ) {
			$error = new \WP_Error( 'invalid_form', 'Something went wrong. Invalid form data.' );
			wp_send_json_error( $error, 500 );
			die();
		}
	}

	static public function is_nonce_verified( $nonce_name, $action, $form_data )
	{
		$nonce_field = $form_data[$nonce_name];
		return isset( $nonce_field ) && wp_verify_nonce( $nonce_field, $action );
	}

	static public function filter_input( $key )
	{
		return filter_input( INPUT_POST, $key, FILTER_SANITIZE_STRING );
	}

}
