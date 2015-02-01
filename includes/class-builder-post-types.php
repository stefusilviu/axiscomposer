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
					'capability_type'     => 'page',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					'query_var'           => 'portfolio',
					'menu_icon'           => 'dashicons-portfolio',
					'rewrite'             => array( 'slug' => 'portfolio', 'with_front' => false, 'feeds' => true, 'pages' => true ),
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
					'has_archive'         => true,
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
