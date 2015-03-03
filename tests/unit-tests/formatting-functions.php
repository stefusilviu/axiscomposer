<?php
/**
 * Test AB formatting functions
 *
 * @since 1.0
 */
class AB_Tests_Formatting_Functions extends AB_Unit_Test_Case {

	/**
	 * Test axisbuilder_clean() - note this is a basic type test as WP core already
	 * has coverage for sanitized_text_field()
	 *
	 * @since 1.0
	 */
	public function test_axisbuilder_clean() {

		$this->assertInternalType( 'string', axisbuilder_clean( 'cleaned' ) );
	}

	/**
	 * Test axisbuilder_array_overlay()
	 *
	 * @since 1.0
	 */
	public function test_axisbuilder_array_overlay() {

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

		$this->assertEquals( $overlayed, axisbuilder_array_overlay( $a1, $a2 ) );
	}

	/**
	 * Test axisbuilder_let_to_num()
	 *
	 * @since 1.0
	 */
	public function test_axisbuilder_let_to_num() {

		$sizes = array(
			'10K' => 10240,
			'10M' => 10485760,
			'10G' => 10737418240,
			'10T' => 10995116277760,
			'10P' => 11258999068426240,
		);

		foreach ( $sizes as $notation => $size ) {
			$this->assertEquals( $size, axisbuilder_let_to_num( $notation ) );
		}
	}

	/**
	 * Test axisbuilder_trim_string()
	 *
	 * @since 1.0
	 */
	public function test_axisbuilder_trim_string() {
		$this->assertEquals( 'string', axisbuilder_trim_string( 'string' ) );
		$this->assertEquals( 's...',   axisbuilder_trim_string( 'string', 4 ) );
		$this->assertEquals( 'st.',    axisbuilder_trim_string( 'string', 3, '.' ) );
	}
}
