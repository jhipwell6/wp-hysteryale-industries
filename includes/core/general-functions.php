<?php

/**
 * Get an item from an array or object using "dot" notation.
 *
 * @param  mixed   $target
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
if ( ! function_exists( 'data_get' ) ) {

	function data_get( $target, $key, $default = null )
	{
		if ( is_null( $key ) )
			return $target;
		foreach ( explode( '.', $key ) as $segment ) {
			if ( is_array( $target ) ) {
				if ( ! array_key_exists( $segment, $target ) ) {
					return $default;
				}
				$target = $target[$segment];
			} elseif ( $target instanceof ArrayAccess ) {
				if ( ! isset( $target[$segment] ) ) {
					return $default;
				}
				$target = $target[$segment];
			} elseif ( is_object( $target ) ) {
				if ( ! isset( $target->{$segment} ) ) {
					return $default;
				}
				$target = $target->{$segment};
			} else {
				return $default;
			}
		}
		return $target;
	}

}

/**
 * Return the first element in an array passing a given truth test.
 *
 * @param  array  $array
 * @param  callable  $callback
 * @param  mixed  $default
 * @return mixed
 */
if ( ! function_exists( 'array_first' ) ) {

	function array_first( $array, $callback = null, $default = null )
	{
		if ( is_null( $callback ) ) {
			return is_array( $array ) && count( $array ) > 0 ? reset( $array ) : null;
		}
		foreach ( $array as $key => $value ) {
			if ( call_user_func( $callback, $key, $value ) )
				return $value;
		}
		return value( $default );
	}

}

/**
 * Return the default value of the given value.
 *
 * @param  mixed  $value
 * @return mixed
 */
if ( ! function_exists( 'value' ) ) {

	function value( $value )
	{
		return $value instanceof Closure ? $value() : $value;
	}

}

/**
 * Check if an array is associative or has numeric keys
 *
 * @param  mixed  $value
 * @return mixed
 */
if ( ! function_exists( 'is_assoc' ) ) {

	function is_assoc( array $arr )
	{
		if ( array() === $arr )
			return false;
		return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}

}

/**
 * Change string case from camel to snake
 *
 * @param  string  $value
 * @return string
 */
if ( ! function_exists( 'decamelize' ) ) {

	function decamelize( $string )
	{
		return strtolower( preg_replace( array( '/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/' ), '$1_$2', $string ) );
	}

}

/**
 * Checks truthy value
 *
 * @param  string  $value
 * @param  bool  $return_null
 * @return bool
 */
if ( ! function_exists( 'is_true' ) ) {

	function is_true( $val, $return_null = false )
	{
		$boolval = ( is_string( $val ) ? filter_var( $val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) : (bool) $val );
		return ( $boolval === null && ! $return_null ? false : $boolval );
	}

}

if ( ! function_exists( 'wp_hyg_promos_rgb_from_hex' ) ) {

	/**
	 * Convert RGB to HEX.
	 *
	 * @param mixed $color Color.
	 *
	 * @return array
	 */
	function wp_hyg_promos_rgb_from_hex( $color )
	{
		$color = str_replace( '#', '', $color );
		// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF".
		$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

		$rgb = array();
		$rgb['R'] = hexdec( $color[0] . $color[1] );
		$rgb['G'] = hexdec( $color[2] . $color[3] );
		$rgb['B'] = hexdec( $color[4] . $color[5] );

		return $rgb;
	}

}

if ( ! function_exists( 'wp_hyg_promos_hex_darker' ) ) {

	/**
	 * Make HEX color darker.
	 *
	 * @param mixed $color  Color.
	 * @param int   $factor Darker factor.
	 *                      Defaults to 30.
	 * @return string
	 */
	function wp_hyg_promos_hex_darker( $color, $factor = 30 )
	{
		$base = wp_hyg_promos_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount = $v / 100;
			$amount = wp_hyg_promos_round( $amount * $factor );
			$new_decimal = $v - $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = '0' . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}

}

if ( ! function_exists( 'wp_hyg_promos_hex_lighter' ) ) {

	/**
	 * Make HEX color lighter.
	 *
	 * @param mixed $color  Color.
	 * @param int   $factor Lighter factor.
	 *                      Defaults to 30.
	 * @return string
	 */
	function wp_hyg_promos_hex_lighter( $color, $factor = 30 )
	{
		$base = wp_hyg_promos_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount = 255 - $v;
			$amount = $amount / 100;
			$amount = wp_hyg_promos_round( $amount * $factor );
			$new_decimal = $v + $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = '0' . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}

}

if ( ! function_exists( 'wp_hyg_promos_hex_is_light' ) ) {

	/**
	 * Determine whether a hex color is light.
	 *
	 * @param mixed $color Color.
	 * @return bool  True if a light color.
	 */
	function wp_hyg_promos_hex_is_light( $color )
	{
		$hex = str_replace( '#', '', $color );

		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		return $brightness > 155;
	}

}

if ( ! function_exists( 'wp_hyg_promos_light_or_dark' ) ) {

	/**
	 * Detect if we should use a light or dark color on a background color.
	 *
	 * @param mixed  $color Color.
	 * @param string $dark  Darkest reference.
	 *                      Defaults to '#000000'.
	 * @param string $light Lightest reference.
	 *                      Defaults to '#FFFFFF'.
	 * @return string
	 */
	function wp_hyg_promos_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' )
	{
		return wp_hyg_promos_hex_is_light( $color ) ? $dark : $light;
	}

}

if ( ! function_exists( 'wp_hyg_promos_format_hex' ) ) {

	/**
	 * Format string as hex.
	 *
	 * @param string $hex HEX color.
	 * @return string|null
	 */
	function wp_hyg_promos_format_hex( $hex )
	{
		$hex = trim( str_replace( '#', '', $hex ) );

		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		return $hex ? '#' . $hex : null;
	}

}

if ( ! function_exists( 'wp_hyg_promos_round' ) ) {

	/**
	 * Round a number using the built-in `round` function, but unless the value to round is numeric
	 * (a number or a string that can be parsed as a number), apply 'floatval' first to it
	 * (so it will convert it to 0 in most cases).
	 *
	 * This is needed because in PHP 7 applying `round` to a non-numeric value returns 0,
	 * but in PHP 8 it throws an error.
	 *
	 * @param mixed $val The value to round.
	 * @param int   $precision The optional number of decimal digits to round to.
	 * @param int   $mode A constant to specify the mode in which rounding occurs.
	 *
	 * @return float The value rounded to the given precision as a float, or the supplied default value.
	 */
	function wp_hyg_promos_round( $val, int $precision = 0, int $mode = PHP_ROUND_HALF_UP ): float
	{
		if ( ! is_numeric( $val ) ) {
			$val = floatval( $val );
		}
		return round( $val, $precision, $mode );
	}

}

if ( ! function_exists( 'wp_hyg_promos_decode' ) ) {

	function wp_hyg_promos_decode( &$string )
	{
		$string = str_replace( array( '’' ), '&rsquo;', $string );
		return $string;
	}

}

if ( ! function_exists( 'array_walk_recursive_array' ) ) {

	function array_walk_recursive_array( array &$array, callable $callback )
	{
		foreach ( $array as $k => &$v ) {
			if ( is_array( $v ) ) {
				array_walk_recursive_array( $v, $callback );
			} else {
				$callback( $v, $k, $array );
			}
		}
	}

}