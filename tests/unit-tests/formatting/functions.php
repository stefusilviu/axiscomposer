<?php

namespace AxisComposer\Tests\Formatting;

/**
 * Class Functions
 * @package AxisComposer\Tests\Formatting
 * @since   1.0.0
 *
 * @todo Split formatting class into smaller classes
 */
class Functions extends \AC_Unit_Test_Case {

	/**
	 * Test ac_clean() - note this is a basic type test as WP core already
	 * has coverage for sanitized_text_field()
	 *
	 * @since 1.0.0
	 */
	public function test_ac_clean() {

		$this->assertEquals( 'cleaned', ac_clean( '<script>alert();</script>cleaned' ) );
	}

	/**
	 * Test ac_sanitize_tooltip() - note this is a basic type test as WP core already
	 * has coverage for wp_kses()
	 *
	 * @since 1.0.0
	 */
	public function test_ac_sanitize_tooltip() {

		$this->assertEquals( 'alert();cleaned', ac_sanitize_tooltip( '<script>alert();</script>cleaned' ) );
	}

	/**
	 * Test ac_array_overlay()
	 *
	 * @since 1.0.0
	 */
	public function test_ac_array_overlay() {

		$a1 = array(
			'apple'      => 'banana',
			'pear'       => 'grape',
			'vegetables' => array(
				'cucumber' => 'asparagus',
			)
		);

		$a2 = array(
			'strawberry' => 'orange',
			'apple'      => 'kiwi',
			'vegetables' => array(
				'cucumber' => 'peas',
			),
		);

		$overlayed = array(
			'apple'      => 'kiwi',
			'pear'       => 'grape',
			'vegetables' => array(
				'cucumber' => 'peas',
			),
		);

		$this->assertEquals( $overlayed, ac_array_overlay( $a1, $a2 ) );
	}

	/**
	 * Test ac_let_to_num()
	 *
	 * @since 1.0.0
	 */
	public function ac_let_to_num() {

		$sizes = array(
			'10K' => 10240,
			'10M' => 10485760,
			'10G' => 10737418240,
			'10T' => 10995116277760,
			'10P' => 11258999068426240,
		);

		foreach ( $sizes as $notation => $size ) {
			$this->assertEquals( $size, ac_let_to_num( $notation ) );
		}
	}

	/**
	 * Test ac_trim_string()
	 *
	 * @since 1.0.0
	 */
	public function test_ac_trim_string() {
		$this->assertEquals( 'string', ac_trim_string( 'string' ) );
		$this->assertEquals( 's...',   ac_trim_string( 'string', 4 ) );
		$this->assertEquals( 'st.',    ac_trim_string( 'string', 3, '.' ) );
	}
}
