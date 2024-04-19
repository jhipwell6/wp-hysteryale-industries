<?php

namespace WP_HYG_Industries\Models;

use \WP_HYG_Industries\Models\Abstracts\Post_Model;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Promo extends Post_Model
{
	const POST_TYPE = 'industry';
	const UNIQUE_KEY = 'industry_id';
	const WP_PROPS = array(
		'post_title' => 'title',
		'post_content' => 'description',
		'post_date' => 'date',
	);
	const ALIASES = array(
	);
	const HIDDEN = array(
	);
	
	// Stored
	protected $title;
	protected $description;
	protected $date;
	protected $image;
	protected $industry_id;
	private $url;
	
	/*
	 * Getters
	 */

	public function get_title()
	{
		return $this->get_post_title();
	}

	public function get_description( $apply_filters = false )
	{
		return $this->get_post_content( $apply_filters );
	}

	public function get_date( $format = 'Y-m-d h:i:s' )
	{
		return $this->get_post_date( $format );
	}
	
	public function get_image()
	{
		if ( null === $this->image ) {
			$post_thumbnail_id = get_post_thumbnail_id( $this->get_id() );
			$this->image = wp_get_attachment_image_url( $post_thumbnail_id, 'full' );
		}
		return $this->image;
	}
	
	public function has_image()
	{
		return (bool) $this->get_image();
	}

	public function get_industry_id()
	{
		return $this->get_prop( 'industry_id' );
	}
	
	public function get_url()
	{
		if ( null === $this->url ) {
			$this->url = get_permalink( $this->get_id() );
		}
		return $this->url;
	}
	
	/*
	 * Setters
	 */

	public function set_title( $value )
	{
		return $this->set_prop( 'title', $value );
	}

	public function set_description( $value )
	{
		return $this->set_prop( 'description', $value );
	}

	public function set_date( $value, $format = 'Y-m-d h:i:s' )
	{
		return $this->set_prop( 'date', $this->to_datetime( $value, $format ) );
	}
	
	public function set_image( $value )
	{
		return $this->set_prop( 'image', $value );
	}
	
	public function set_industry_id( $value )
	{
		return $this->set_prop( 'industry_id', $value );
	}
	
	/*
	 * Savers
	 */

	public function save_title_meta( $value )
	{
		return $this->save_post_title( $value );
	}

	public function save_description_meta( $value )
	{
		if ( is_array( $value ) || ! $value ) {
			$value = ' ';
		}
		return $this->save_post_content( $value );
	}

	public function save_date_meta( $value, $return_format = '' )
	{
		return $this->save_post_date( $this->to_datetime( $value ), $return_format );
	}
	
	public function save_image_meta( $value )
	{
		$image_id = $this->process_image( 'image', $value );
		return set_post_thumbnail( $this->get_id(), $image_id );
	}
	
	/*
	 * Helpers
	 */
	
	private function process_image( $prop, $value )
	{
		$image = WP_HYG_Industries()->Media()->get_image_from_library_by_url( $value );
		if ( ! $image ) {
			$image = WP_HYG_Industries()->Media()->sideload_image( $value, 0, null, 'id' );
		}
		return is_object( $image ) ? $image->ID : $image;
	}
}