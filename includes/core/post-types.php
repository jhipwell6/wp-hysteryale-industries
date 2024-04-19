<?php

namespace WP_HYG_Industries\Core;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Post_Types
{
	protected static $instance;
	public static $post_types = array(
		'industry' => array(
			'labels' => array(
				'singular' => 'Industry',
				'plural' => 'Industries',
			),
			'config' => array(
				'hierarchical' => true,
				'has_archive' => false,
				'publicly_queryable' => true,
				'supports' => array( 'title', 'editor', 'thumbnail' ),
				'menu_icon' => 'dashicons-admin-tools',
			),
		),
	);
	
	public static $taxonomies = array(
		'industry_group' => array(
			'labels' => array(
				'singular' => 'Type',
				'plural' => 'Types',
			),
			'config' => array(
				'publicly_queryable' => false,
				'has_archive' => false,
				'hierarchical' => true,
			),
			'post_type' => [ 'industry' ],
		),
	);

	/**
	 * Initializes variables and sets up WordPress hooks/actions.
	 * @return void
	 */
	protected function __construct()
	{
		add_action( 'init', [ $this, 'register_post_types' ] );
		add_action( 'init', [ $this, 'register_taxonomies' ] );
	}

	/**
	 * Static Singleton Factory Method
	 * @return self
	 */
	public static function instance()
	{
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function get_custom_post_types()
	{
		return array_keys( self::$post_types );
	}

	public static function get_public_post_types()
	{
		return get_post_types( [
			'public' => true,
			] );
	}

	public static function get_main_post_types()
	{
		$post_types = get_post_types( [
			'public' => true,
			'exclude_from_search' => false,
			] );

		if ( isset( $post_types['attachment'] ) ) {
			unset( $post_types['attachment'] );
		}

		return $post_types;
	}

	public function register_post_types()
	{
		foreach ( self::$post_types as $post_type => $settings ) {
			$args = $this->get_post_type_config( $post_type, $settings );
			register_post_type( $post_type, $args );
		}
	}

	public function register_taxonomies()
	{
		foreach ( self::$taxonomies as $taxonomy => $settings ) {
			$args = $this->get_taxonomy_config( $taxonomy, $settings );
			register_taxonomy( $taxonomy, $settings['post_type'], $args );
			register_taxonomy_for_object_type( $taxonomy, $settings['post_type'] ); // better safe than sorry
		}
	}

	private function get_post_type_config( $post_type, $settings )
	{
		$defaults = array(
			'label' => $settings['labels']['plural'],
			'labels' => $this->get_post_type_labels( $settings['labels'] ),
			'description' => '',
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_rest' => true,
			'rest_base' => '',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'rest_namespace' => 'wp/v2',
			'has_archive' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'delete_with_user' => false,
			'exclude_from_search' => false,
			'capability_type' => 'post',
			'map_meta_cap' => true,
			'hierarchical' => false,
			'can_export' => true,
			'rewrite' => array( 'slug' => $post_type, 'with_front' => false ),
			'query_var' => true,
			'menu_icon' => 'dashicons-text-page',
			'supports' => array( 'title', 'editor', 'custom-fields' ),
			'show_in_graphql' => false,
		);

		return wp_parse_args( $settings['config'], $defaults );
	}

	private function get_post_type_labels( $labels )
	{
		$singular = __( $labels['singular'], WP_HYG_PROMOS_TEXT_DOMAIN );
		$plural = __( $labels['plural'], WP_HYG_PROMOS_TEXT_DOMAIN );
		return array(
			'name' => $plural,
			'singular_name' => $singular,
			'menu_name' => $plural,
			'all_items' => 'All ' . $plural,
			'add_new' => 'Add New',
			'add_new_item' => 'Add new ' . $singular,
			'edit_item' => 'Edit ' . $singular,
			'new_item' => 'New ' . $singular,
			'view_item' => 'View ' . $singular,
			'view_items' => 'View ' . $plural,
			'search_items' => 'Search ' . $plural,
			'not_found' => 'No ' . $plural . ' found',
			'not_found_in_trash' => 'No ' . $plural . ' found in trash',
			'parent' => 'Parent ' . $singular . ':',
			'featured_image' => 'Featured image for this ' . $singular,
			'set_featured_image' => 'Set featured image for this ' . $singular,
			'remove_featured_image' => 'Remove featured image for this ' . $singular,
			'use_featured_image' => 'Use as featured image for this ' . $singular,
			'archives' => $singular . ' archives',
			'insert_into_item' => 'Insert into ' . $singular,
			'uploaded_to_this_item' => 'Upload to this ' . $singular,
			'filter_items_list' => 'Filter ' . $plural . ' list',
			'items_list_navigation' => $plural . ' list navigation',
			'items_list' => $plural . ' list',
			'attributes' => $plural . ' attributes',
			'name_admin_bar' => $singular,
			'item_published' => $singular . ' published',
			'item_published_privately' => $singular . ' published privately.',
			'item_reverted_to_draft' => $singular . ' reverted to draft.',
			'item_scheduled' => $singular . ' scheduled',
			'item_updated' => $singular . ' updated.',
			'parent_item_colon' => 'Parent ' . $singular . ':',
		);
	}
	
	private function get_taxonomy_config( $taxonomy, $settings )
	{
		$defaults = array(
			'label' => $settings['labels']['plural'],
			'labels' => $this->get_taxonomy_labels( $settings['labels'] ),
			'hierarchical' => true,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => $taxonomy ),
			'show_in_rest' => true,
		);

		return wp_parse_args( $settings['config'], $defaults );
	}
	
	private function get_taxonomy_labels( $labels )
	{
		return array(
			'name' => $labels['singular'],
			'singular_name' => $labels['singular'],
			'search_items' => 'Search ' . $labels['plural'],
			'all_items' => 'All ' . $labels['plural'],
			'view_item' => 'View ' . $labels['singular'],
			'parent_item' => 'Parent ' . $labels['singular'],
			'parent_item_colon' => 'Parent ' . $labels['singular'] . ':',
			'edit_item' => 'Edit ' . $labels['singular'],
			'update_item' => 'Update ' . $labels['singular'],
			'add_new_item' => 'Add new ' . $labels['singular'],
			'new_item_name' => 'New ' . $labels['singular'] . ' Name',
			'not_found' => 'No ' . $labels['plural'] . ' found',
			'back_to_items' => 'Back to ' . $labels['plural'],
			'menu_name' => $labels['singular'],
		);
	}

}

Post_Types::instance();
