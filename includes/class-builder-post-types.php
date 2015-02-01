<?php
/**
 * Post types
 *
 * Registers post types and taxonomies.
 *
 * @class       AB_Post_Types
 * @package     AxisBuilder/Classes/Portfolio
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */
class AB_Post_Types {

	/**
	 * Hook in methods
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {
		if ( taxonomy_exists( 'portfolio_type' ) ) {
			return;
		}

		do_action( 'axisbuilder_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( post_type_exists( 'portfolio' ) ) {
			return;
		}

		do_action( 'axisbuilder_register_post_type' );

		register_post_type( 'portfolio',
			apply_filters( 'axisbuilder_register_post_type_portfolio',
				array(
					'labels' => array(
						'name'               => __( 'Portfolios', 'axisbuilder' ),
						'singular_name'      => __( 'Portfolio', 'axisbuilder' ),
						'menu_name'          => _x( 'Portfolios', 'Admin menu name', 'axisbuilder' ),
						'add_new'            => __( 'Add Portfolio', 'axisbuilder' ),
						'add_new_item'       => __( 'Add New Portfolio', 'axisbuilder' ),
						'edit'               => __( 'Edit', 'axisbuilder' ),
						'edit_item'          => __( 'Edit Portfolio', 'axisbuilder' ),
						'new_item'           => __( 'New Portfolio', 'axisbuilder' ),
						'view'               => __( 'View Portfolio', 'axisbuilder' ),
						'view_item'          => __( 'View Portfolio', 'axisbuilder' ),
						'search_items'       => __( 'Search Portfolios', 'axisbuilder' ),
						'not_found'          => __( 'No Portfolios found', 'axisbuilder' ),
						'not_found_in_trash' => __( 'No Portfolios found in trash', 'axisbuilder' ),
						'parent'             => __( 'Parent Portfolio', 'axisbuilder' )
					),
					'description'         => __( 'This is where you can add new portfolios to your store.', 'axisbuilder' ),
					'public'              => true,
					'show_ui'             => true,
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => true,
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
					'has_archive'         => false,
					'show_in_nav_menus'   => true
				)
			)
		);
	}

	/**
	 * Add Portfolio Support to Jetpack Omnisearch
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'portfolio' );
		}
	}

}

AB_Post_Types::init();
