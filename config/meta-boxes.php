<?php
global $builder;

$boxes = array(
	array( 'title' =>__( 'Layout Settings', 'axisbuilder' ), 'id' => 'layout', 'page' => array( 'portfolio', 'page' , 'post' ), 'context' => 'side', 'priority' => 'high' ),
);

$boxes = apply_filters( 'axisbuilder_meta_boxes', $boxes );

$elements = array(

	array(
		"slug"      => 'axisbuilder',
		"name"      => __( 'Axis Page Builder', 'axisbuilder' ),
		"id"        => 'layout_editor',
		"type"      => array( $builder, 'visual_editor'),
		"tab_order" => array(
			__( 'Layout Elements',  'axisbuilder' ),
			__( 'Content Elements', 'axisbuilder' ),
			__( 'Media Elements',   'axisbuilder' )
		),
		"desc"      =>  '<h4>' . __( 'Quick Info & Hotkeys', 'axisbuilder' ) . "</h4>".
						'<strong>' . __( 'General Info', 'axisbuilder' ) . '</strong>' .
						"<ul>" .
						'	<li>' . __( 'To insert an Element either click the insert button for that element or drag the button onto the canvas', 'axisbuilder' ) . '</li>' .
						'	<li>' . __( 'If you place your mouse above the insert button a short info tooltip will appear', 'axisbuilder' ) . '</li>' .
						'	<li>' . __( 'To sort and arrange your elements just drag them to a position of your choice and release them', 'axisbuilder' ) . '</li>' .
						'	<li>' . __( 'Valid drop targets will be highlighted. Some elements like fullwidth sliders and color section can not be dropped onto other elements', 'axisbuilder' ) . '</li>' .
						"</ul>" .
						'<strong>' . __( 'Edit Elements in Popup Window:', 'axisbuilder' ) . '</strong>' .
						"<ul>" .
						'	<li>' . __( 'Most elements open a popup window if you click them', 'axisbuilder' ) . '</li>' .
						'	<li>' . __( 'Press TAB to navigate trough the various form fields of a popup window.', 'axisbuilder' ) . '</li>' .
						'	<li>' . __( 'Press ESC on your keyboard or the Close Button to close popup window.', 'axisbuilder' ) . '</li>' .
						'	<li>' . __( 'Press ENTER on your keyboard or the Save Button to save current state of a popup window', 'axisbuilder' ) . '</li>' .
						"</ul>"
	),

	array(
		'slug'    => 'layout',
		'name'    => __( 'Layout', 'axisbuilder' ),
		'desc'    => __( 'Select the desired Page layout', 'axisbuilder' ),
		'id'      => 'layout',
		'type'    => 'select',
		'std'     => '',
		'class'   => 'avia-style',
		'subtype' => array(
			__( 'Default Layout', 'axisbuilder' ) => '',
			__( 'No Sidebar',     'axisbuilder' ) => 'fullsize',
			__( 'Left Sidebar',   'axisbuilder' ) => 'sidebar_left',
			__( 'Right Sidebar',  'axisbuilder' ) => 'sidebar_right',
		)
	),

	array(
		'slug'     => 'layout',
		'name'     => __( 'Sidebar Setting', 'axisbuilder' ),
		'desc'     => __( 'Choose a custom sidebar for this entry', 'axisbuilder' ),
		'id'       => 'sidebar',
		'type'     => 'select',
		'std'      => '',
		'class'    => 'avia-style',
		'required' => array( 'layout', 'not', 'fullsize' ),
		// 'subtype'  => AviaHelper::get_registered_sidebars( array( 'Default Sidebars' => '' ), array( 'Displayed Everywhere' ) )
	),

	array(
		'slug'    => 'layout',
		'name'    => __( 'Title Bar Settings', 'axisbuilder' ),
		'desc'    => __( 'Display the Title Bar with Page Title and Breadcrumb Navigation?', 'axisbuilder' ),
		'id'      => 'header_title_bar',
		'type'    => 'select',
		'std'     => '',
		'class'   => 'avia-style',
		'subtype' => array(
			__( 'Default Layout',                'axisbuilder' ) => '',
			__( 'Display title and breadcrumbs', 'axisbuilder' ) => 'title_bar_breadcrumb',
			__( 'Display only title',            'axisbuilder' ) => 'title_bar',
			__( 'Hide both',                     'axisbuilder' ) => 'hidden_title_bar',
		)
	),

	array(
		'slug'    => 'layout',
		'name'    => __( 'Activate Header transparency', 'axisbuilder' ),
		'desc'    => __( 'If checked the header will be transparent and once the user scrolls down it will fade in.', 'axisbuilder' ),
		'id'      => 'header_transparency',
		'type'    => 'select',
		'std'     => '',
		'class'   => 'avia-style',
		'subtype' => array(
			__( 'No transparency',             'axisbuilder' ) => '',
			__( 'Transparent Header',          'axisbuilder' ) => 'header_transparent',
			__( 'Transparent & Glassy Header', 'axisbuilder' ) => 'header_transparent header_glassy '
		)
	),

	array(
		'slug'    => 'layout',
		'name'    => __( 'Footer Settings', 'axisbuilder' ),
		'desc'    => __( 'Display the footer widgets?', 'axisbuilder' ),
		'id'      => 'footer',
		'type'    => 'select',
		'std'     => '',
		'class'   => 'avia-style',
		'subtype' => array(
			__( 'Both Widgets and Socket',  'axisbuilder' ) => 'footer_both',
			__( 'Only Widgets (No Socket)', 'axisbuilder' ) => 'widget_only',
			__( 'Only Socket (No Widgets)', 'axisbuilder' ) => 'socket_only',
			__( 'Don\'t Display Both',      'axisbuilder' ) => 'footer_none'
		)
	),
);

$elements = apply_filters( 'axisbuilder_meta_elements', $elements );
