<?php
/**
 * Post types
 *
 * Registers post types and taxonomies.
 *
 * @class    AC_Post_Types
 * @version  1.0.0
 * @package  AxisComposer/Classes/Portfolio
 * @category Class
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Post_Types Class
 */
class AC_Post_Types {

	/**
	 * Hook in methods.
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

		do_action( 'axiscomposer_register_taxonomy' );

		$permalinks = get_option( 'axiscomposer_permalinks' );

		register_taxonomy( 'portfolio_type',
			apply_filters( 'axiscomposer_taxonomy_objects_portfolio_type', array( 'portfolio' ) ),
			apply_filters( 'axiscomposer_taxonomy_args_portfolio_type', array(
				'hierarchical'      => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'query_var'         => is_admin(),
				'rewrite'           => false,
				'public'            => false
			) )
		);

		register_taxonomy( 'portfolio_cat',
			apply_filters( 'axiscomposer_taxonomy_objects_portfolio_cat', array( 'portfolio' ) ),
			apply_filters( 'axiscomposer_taxonomy_args_portfolio_cat', array(
				'hierarchical' => true,
				'label'        => __( 'Project Categories', 'axiscomposer' ),
				'labels'       => array(
						'name'              => __( 'Project Categories', 'axiscomposer' ),
						'singular_name'     => __( 'Project Category', 'axiscomposer' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'axiscomposer' ),
						'search_items'      => __( 'Search Project Categories', 'axiscomposer' ),
						'all_items'         => __( 'All Project Categories', 'axiscomposer' ),
						'parent_item'       => __( 'Parent Project Category', 'axiscomposer' ),
						'parent_item_colon' => __( 'Parent Project Category:', 'axiscomposer' ),
						'edit_item'         => __( 'Edit Project Category', 'axiscomposer' ),
						'update_item'       => __( 'Update Project Category', 'axiscomposer' ),
						'add_new_item'      => __( 'Add New Project Category', 'axiscomposer' ),
						'new_item_name'     => __( 'New Project Category Name', 'axiscomposer' )
					),
				'show_ui'      => true,
				'query_var'    => true,
				'capabilities' => array(
					'manage_terms' => 'manage_portfolio_terms',
					'edit_terms'   => 'edit_portfolio_terms',
					'delete_terms' => 'delete_portfolio_terms',
					'assign_terms' => 'assign_portfolio_terms',
				),
				'rewrite'      => array(
					'slug'         => empty( $permalinks['category_base'] ) ? _x( 'portfolio-category', 'slug', 'axiscomposer' ) : $permalinks['category_base'],
					'with_front'   => false,
					'hierarchical' => true,
				),
			) )
		);

		register_taxonomy( 'portfolio_tag',
			apply_filters( 'axiscomposer_taxonomy_objects_portfolio_tag', array( 'portfolio' ) ),
			apply_filters( 'axiscomposer_taxonomy_args_portfolio_tag', array(
				'hierarchical' => false,
				'label'        => __( 'Project Tags', 'axiscomposer' ),
				'labels'       => array(
						'name'                       => __( 'Project Tags', 'axiscomposer' ),
						'singular_name'              => __( 'Project Tag', 'axiscomposer' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'axiscomposer' ),
						'search_items'               => __( 'Search Project Tags', 'axiscomposer' ),
						'all_items'                  => __( 'All Project Tags', 'axiscomposer' ),
						'edit_item'                  => __( 'Edit Project Tag', 'axiscomposer' ),
						'update_item'                => __( 'Update Project Tag', 'axiscomposer' ),
						'add_new_item'               => __( 'Add New Project Tag', 'axiscomposer' ),
						'new_item_name'              => __( 'New Project Tag Name', 'axiscomposer' ),
						'popular_items'              => __( 'Popular Project Tags', 'axiscomposer' ),
						'separate_items_with_commas' => __( 'Separate Project Tags with commas', 'axiscomposer' ),
						'add_or_remove_items'        => __( 'Add or remove Project Tags', 'axiscomposer' ),
						'choose_from_most_used'      => __( 'Choose from the most used Project tags', 'axiscomposer' ),
						'not_found'                  => __( 'No Project Tags found', 'axiscomposer' ),
					),
				'show_ui'      => true,
				'query_var'    => true,
				'capabilities' => array(
					'manage_terms' => 'manage_portfolio_terms',
					'edit_terms'   => 'edit_portfolio_terms',
					'delete_terms' => 'delete_portfolio_terms',
					'assign_terms' => 'assign_portfolio_terms',
				),
				'rewrite'      => array(
					'slug'       => empty( $permalinks['tag_base'] ) ? _x( 'portfolio-tag', 'slug', 'axiscomposer' ) : $permalinks['tag_base'],
					'with_front' => false
				),
			) )
		);

		do_action( 'axiscomposer_after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( post_type_exists( 'portfolio' ) ) {
			return;
		}

		do_action( 'axiscomposer_register_post_type' );

		$permalinks          = get_option( 'axiscomposer_permalinks' );
		$portfolio_permalink = empty( $permalinks['portfolio_base'] ) ? _x( 'portfolio', 'slug', 'axiscomposer' ) : $permalinks['portfolio_base'];

		register_post_type( 'portfolio',
			apply_filters( 'axiscomposer_register_post_type_portfolio',
				array(
					'labels'              => array(
							'name'                  => __( 'Projects', 'axiscomposer' ),
							'singular_name'         => __( 'Project', 'axiscomposer' ),
							'menu_name'             => _x( 'Portfolio', 'Admin menu name', 'axiscomposer' ),
							'all_items'             => __( 'All Projects', 'axiscomposer' ),
							'add_new'               => __( 'Add Project', 'axiscomposer' ),
							'add_new_item'          => __( 'Add New Project', 'axiscomposer' ),
							'edit'                  => __( 'Edit', 'axiscomposer' ),
							'edit_item'             => __( 'Edit Project', 'axiscomposer' ),
							'new_item'              => __( 'New Project', 'axiscomposer' ),
							'view'                  => __( 'View Project', 'axiscomposer' ),
							'view_item'             => __( 'View Project', 'axiscomposer' ),
							'search_items'          => __( 'Search Projects', 'axiscomposer' ),
							'not_found'             => __( 'No Projects found', 'axiscomposer' ),
							'not_found_in_trash'    => __( 'No Projects found in trash', 'axiscomposer' ),
							'parent'                => __( 'Parent Project', 'axiscomposer' ),
							'featured_image'        => __( 'Project Image', 'axiscomposer' ),
							'set_featured_image'    => __( 'Set project image', 'axiscomposer' ),
							'remove_featured_image' => __( 'Remove project image', 'axiscomposer' ),
							'use_featured_image'    => __( 'Use as project image', 'axiscomposer' ),

						),
					'description'         => __( 'This is where you can add new portfolio items to your project.', 'axiscomposer' ),
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'portfolio',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					'query_var'           => true,
					'menu_icon'           => 'dashicons-portfolio',
					'rewrite'             => $portfolio_permalink ? array( 'slug' => untrailingslashit( $portfolio_permalink ), 'with_front' => false, 'feeds' => true ) : false,
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
					'has_archive'         => true,
					'show_in_nav_menus'   => true
				)
			)
		);
	}

	/**
	 * Add Portfolio Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'portfolio' );
		}
	}
}

AC_Post_Types::init();
