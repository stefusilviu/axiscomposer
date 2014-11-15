<?php
/**
 * Test AB formatting functions
 *
 * @since 1.0
 */
class AB_Tests_Formatting_Functions extends AB_Unit_Test_Case {

	/**
	 * Test ab_let_to_num()
	 *
	 * @since 1.0
	 */
	public function test_ab_let_to_num() {

		$sizes = array(
			'10K' => 10240,
			'10M' => 10485760,
			'10G' => 10737418240,
			'10T' => 10995116277760,
			'10P' => 11258999068426240,
		);

		foreach ( $sizes as $notation => $size ) {
			$this->assertEquals( $size, ab_let_to_num( $notation ) );
		}
	}
}
