<?php
/**
 * Advertisement Widget
 *
 * @extends 	AB_Widget
 * @package     AxisBuilder/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class AB_Widget_Advertisement extends AB_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'axisbuilder widget_advertisement';
		$this->widget_description = __( 'Displays Advertisements Slots.', 'axisbuilder' );
		$this->widget_id          = 'axisbuilder_widget_advertisement';
		$this->widget_name        = __( 'Axis Builder Advertisement', 'axisbuilder' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Advertisement', 'axisbuilder' ),
				'label' => __( 'Title', 'axisbuilder' )
			),
			'slot_type' => array(
				'type'  => 'select',
				'std'   => 'double',
				'label' => __( 'Slot type', 'axisbuilder' ),
				'options' => array(
					'single'  => __( 'One Slot - 250x250px', 'axisbuilder' ),
					'double'  => __( 'Two Slot - 125x125px', 'axisbuilder' )
				)
			),
			'slot_one_banner'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Banner #1 Image Link', 'axisbuilder' )
			),
			'slot_one_referal'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Banner #1 Referal Link', 'axisbuilder' )
			),
			'slot_two_banner'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Banner #2 Image Link', 'axisbuilder' )
			),
			'slot_two_referal'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Banner #2 Referal Link', 'axisbuilder' )
			),
			'hide_if_target' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Open link in a new window/tab', 'axisbuilder' )
			)
		);
		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {

		extract( $args );

		$title = $instance['title'];
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$slot_type 	    = isset( $instance['slot_type'] ) ? $instance['display_type'] : 'double';
		$hide_if_target = empty( $instance['hide_if_target'] ) ? 0 : 1;

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo $slot_type;

		echo $after_widget;
	}
}
