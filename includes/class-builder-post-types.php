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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Post_Types Class
 */
class AB_Post_Types {

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

		do_action( 'axisbuilder_register_taxonomy' );

		$permalinks = get_option( 'axisbuilder_permalinks' );

		register_taxonomy( 'portfolio_type',
			apply_filters( 'axisbuilder_taxonomy_objects_portfolio_type', array( 'portfolio' ) ),
			apply_filters( 'axisbuilder_taxonomy_args_portfolio_type', array(
				'hierarchical'      => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'query_var'         => is_admin(),
				'rewrite'           => false,
				'public'            => false
			) )
		);

		register_taxonomy( 'portfolio_cat',
			apply_filters( 'axisbuilder_taxonomy_objects_portfolio_cat', array( 'portfolio' ) ),
			apply_filters( 'axisbuilder_taxonomy_args_portfolio_cat', array(
				'hierarchical'          => true,
				'update_count_callback' => '_axisbuilder_term_recount',
				'label'                 => __( 'Project Categories', 'axisbuilder' ),
				'labels' => array(
						'name'              => __( 'Project Categories', 'axisbuilder' ),
						'singular_name'     => __( 'Project Category', 'axisbuilder' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'axisbuilder' ),
						'search_items'      => __( 'Search Project Categories', 'axisbuilder' ),
						'all_items'         => __( 'All Project Categories', 'axisbuilder' ),
						'parent_item'       => __( 'Parent Project Category', 'axisbuilder' ),
						'parent_item_colon' => __( 'Parent Project Category:', 'axisbuilder' ),
						'edit_item'         => __( 'Edit Project Category', 'axisbuilder' ),
						'update_item'       => __( 'Update Project Category', 'axisbuilder' ),
						'add_new_item'      => __( 'Add New Project Category', 'axisbuilder' ),
						'new_item_name'     => __( 'New Project Category Name', 'axisbuilder' )
					),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_portfolio_terms',
					'edit_terms'   => 'edit_portfolio_terms',
					'delete_terms' => 'delete_portfolio_terms',
					'assign_terms' => 'assign_portfolio_terms',
				),
				'rewrite'               => array(
					'slug'         => empty( $permalinks['category_base'] ) ? _x( 'portfolio-category', 'slug', 'axisbuilder' ) : $permalinks['category_base'],
					'with_front'   => false,
					'hierarchical' => true,
				),
			) )
		);

		register_taxonomy( 'portfolio_tag',
			apply_filters( 'axisbuilder_taxonomy_objects_portfolio_tag', array( 'portfolio' ) ),
			apply_filters( 'axisbuilder_taxonomy_args_portfolio_tag', array(
				'hierarchical'          => false,
				'update_count_callback' => '_axisbuilder_term_recount',
				'label'                 => __( 'Project Tags', 'axisbuilder' ),
				'labels'                => array(
						'name'                       => __( 'Project Tags', 'axisbuilder' ),
						'singular_name'              => __( 'Project Tag', 'axisbuilder' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'axisbuilder' ),
						'search_items'               => __( 'Search Project Tags', 'axisbuilder' ),
						'all_items'                  => __( 'All Project Tags', 'axisbuilder' ),
						'edit_item'                  => __( 'Edit Project Tag', 'axisbuilder' ),
						'update_item'                => __( 'Update Project Tag', 'axisbuilder' ),
						'add_new_item'               => __( 'Add New Project Tag', 'axisbuilder' ),
						'new_item_name'              => __( 'New Project Tag Name', 'axisbuilder' ),
						'popular_items'              => __( 'Popular Project Tags', 'axisbuilder' ),
						'separate_items_with_commas' => __( 'Separate Project Tags with commas', 'axisbuilder'  ),
						'add_or_remove_items'        => __( 'Add or remove Project Tags', 'axisbuilder' ),
						'choose_from_most_used'      => __( 'Choose from the most used Project tags', 'axisbuilder' ),
						'not_found'                  => __( 'No Project Tags found', 'axisbuilder' ),
					),
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_portfolio_terms',
					'edit_terms'   => 'edit_portfolio_terms',
					'delete_terms' => 'delete_portfolio_terms',
					'assign_terms' => 'assign_portfolio_terms',
				),
				'rewrite'               => array(
					'slug'       => empty( $permalinks['tag_base'] ) ? _x( 'portfolio-tag', 'slug', 'axisbuilder' ) : $permalinks['tag_base'],
					'with_front' => false
				),
			) )
		);

		do_action( 'axisbuilder_after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( post_type_exists( 'portfolio' ) ) {
			return;
		}

		do_action( 'axisbuilder_register_post_type' );

		$permalinks          = get_option( 'axisbuilder_permalinks' );
		$portfolio_permalink = empty( $permalinks['portfolio_base'] ) ? _x( 'portfolio', 'slug', 'axisbuilder' ) : $permalinks['portfolio_base'];

		register_post_type( 'portfolio',
			apply_filters( 'axisbuilder_register_post_type_portfolio',
				array(
					'labels' => array(
							'name'               => __( 'Projects', 'axisbuilder' ),
							'singular_name'      => __( 'Project', 'axisbuilder' ),
							'menu_name'          => _x( 'Portfolio', 'Admin menu name', 'axisbuilder' ),
							'all_items'          => __( 'All Projects', 'axisbuilder' ),
							'add_new'            => __( 'Add Project', 'axisbuilder' ),
							'add_new_item'       => __( 'Add New Project', 'axisbuilder' ),
							'edit'               => __( 'Edit', 'axisbuilder' ),
							'edit_item'          => __( 'Edit Project', 'axisbuilder' ),
							'new_item'           => __( 'New Project', 'axisbuilder' ),
							'view'               => __( 'View Project', 'axisbuilder' ),
							'view_item'          => __( 'View Project', 'axisbuilder' ),
							'search_items'       => __( 'Search Projects', 'axisbuilder' ),
							'not_found'          => __( 'No Projects found', 'axisbuilder' ),
							'not_found_in_trash' => __( 'No Projects found in trash', 'axisbuilder' ),
							'parent'             => __( 'Parent Project', 'axisbuilder' )
						),
					'description'         => __( 'This is where you can add new portfolio items to your project.', 'axisbuilder' ),
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

AB_Post_Types::init();
