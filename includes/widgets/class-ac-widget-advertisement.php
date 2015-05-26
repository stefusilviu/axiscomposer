<?php
/**
 * Advertisement Widget
 *
 * Displays Advertisements Slots widget.
 *
 * @extends     AC_Widget
 * @package     AxisComposer/Widgets
 * @category    Widgets
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_Widget_Advertisement extends AC_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'axiscomposer widget_advertisement';
		$this->widget_description = __( 'Displays Advertisements Slots.', 'axiscomposer' );
		$this->widget_id          = 'axiscomposer_widget_advertisement';
		$this->widget_name        = __( 'AxisComposer Advertisement', 'axiscomposer' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Advertisement', 'axiscomposer' ),
				'label' => __( 'Title', 'axiscomposer' )
			),
			'slot_type' => array(
				'type'  => 'select',
				'std'   => 'double',
				'label' => __( 'Slot type', 'axiscomposer' ),
				'options' => array(
					'single'  => __( 'One Slot - 250x250px', 'axiscomposer' ),
					'double'  => __( 'Two Slot - 125x125px', 'axiscomposer' )
				)
			),
			'slot_one_banner'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Banner #1 Image Link', 'axiscomposer' )
			),
			'slot_one_referal'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Banner #1 Referal Link', 'axiscomposer' )
			),
			'slot_two_banner'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Banner #2 Image Link', 'axiscomposer' )
			),
			'slot_two_referal'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Banner #2 Referal Link', 'axiscomposer' )
			),
			'hide_if_target' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Open link in a new window/tab', 'axiscomposer' )
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

		$slot_type      = isset( $instance['slot_type'] ) ? $instance['display_type'] : 'double';
		$hide_if_target = empty( $instance['hide_if_target'] ) ? 0 : 1;

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		if ( $hide_if_target ) {
			echo '<div class="hide_advertisement_widget_if_target"></div>';
		}

		echo $slot_type;

		echo $after_widget;
	}
}
