<?php
namespace WP_HYG_Industries\Core;

if ( ! defined('ABSPATH') )
    exit;

class Industry_Factory extends Abstracts\Factory
{
    private $found = array();
	
    /**
     * Get an item from the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */	
	public function get( $Industry = false, $default = null )
    {		
		$industry_id = $this->get_industry_id( $Industry );
		if ( $industry_id && $this->contains( 'id', $industry_id ) && $industry_id != 0 && ! in_array( $industry_id, $this->found ) ) {
			$this->found[] = $industry_id;
            return $this->where( 'id', $industry_id );
        }
		
		$Industry = new \WP_HYG_Industries\Models\Industry( $industry_id );
		$this->add( $Industry );
		
        return $this->last();
    }
	
	/**
	 * Get the industry ID depending on what was passed.
	 *
	 * @return int|bool false on failure
	 */
	private function get_industry_id( $Industry )
	{
		global $post;

		if ( false === $Industry && isset( $post, $post->ID ) && 'industry' === get_post_type( $post->ID ) ) {
			return absint( $post->ID );
		} elseif ( is_numeric( $Industry ) ) {
			return $Industry;
		} elseif ( $Industry instanceof \WP_HYG_Industries\Models\Industry ) {
			return $Industry->get_id();
		} elseif ( ! empty( $Industry->ID ) ) {
			return $Industry->ID;
		} else {
			return false;
		}
	}
}