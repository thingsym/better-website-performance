<?php
/**
 * Class Test_Customizer_Sanitize
 *
 * @package Better_Website_Performance
 */

use \Better_Website_Performance\Customizer\Sanitize;

class Test_Customizer_Sanitize extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_checkbox_boolean() {
		$setting = New WP_Customize_Setting(
			'test',
			'test',
			[
				'default' => 0,
			]
		);

		$this->assertTrue( Sanitize::sanitize_checkbox_boolean( true, $setting ) );
		$this->assertFalse( Sanitize::sanitize_checkbox_boolean( false, $setting ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_number_absint_empty() {
		$setting = New WP_Customize_Setting(
			'test',
			'test',
			[]
		);

		$this->assertSame( 1, Sanitize::sanitize_number_absint( 1, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number_absint( -1, $setting ) );
		$this->assertSame( '', Sanitize::sanitize_number_absint( 0, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number_absint( '1', $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number_absint( '-1', $setting ) );
		$this->assertSame( '', Sanitize::sanitize_number_absint( '0', $setting ) );
		$this->assertSame( '', Sanitize::sanitize_number_absint( '', $setting ) );
		$this->assertSame( '', Sanitize::sanitize_number_absint( 'aaa', $setting ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_number_absint_zero() {
		$setting = New WP_Customize_Setting(
			'test',
			'test',
			[
				'default' => 0,
			]
		);

		$this->assertSame( 1, Sanitize::sanitize_number_absint( 1, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number_absint( -1, $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number_absint( 0, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number_absint( '1', $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number_absint( '-1', $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number_absint( '0', $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number_absint( '', $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number_absint( 'aaa', $setting ) );

	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_number_absint_string() {
		$setting = New WP_Customize_Setting(
			'test',
			'test',
			[
				'default' => 'a',
			]
		);

		$this->assertSame( 1, Sanitize::sanitize_number_absint( 1, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number_absint( -1, $setting ) );
		$this->assertSame( 'a', Sanitize::sanitize_number_absint( 0, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number_absint( '1', $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number_absint( '-1', $setting ) );
		$this->assertSame( 'a', Sanitize::sanitize_number_absint( '0', $setting ) );
		$this->assertSame( 'a', Sanitize::sanitize_number_absint( '', $setting ) );
		$this->assertSame( 'a', Sanitize::sanitize_number_absint( 'aaa', $setting ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_positive_number_empty() {
		$setting = New WP_Customize_Setting(
			'test',
			'test',
			[]
		);

		$this->assertSame( 1, Sanitize::sanitize_positive_number( 1, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( -1, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( 0, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( '1', $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( '-1', $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( '0', $setting ) );
		$this->assertSame( '', Sanitize::sanitize_positive_number( '', $setting ) );
		$this->assertSame( '', Sanitize::sanitize_positive_number( 'aaa', $setting ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_positive_number_zero() {
		$setting = New WP_Customize_Setting(
			'test',
			'test',
			[
				'default' => 0,
			]
		);

		$this->assertSame( 1, Sanitize::sanitize_positive_number( 1, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( -1, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( 0, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( '1', $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( '-1', $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( '0', $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_positive_number( '', $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_positive_number( 'aaa', $setting ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_positive_string() {
		$setting = New WP_Customize_Setting(
			'test',
			'test',
			[
				'default' => 'a',
			]
		);

		$this->assertSame( 1, Sanitize::sanitize_positive_number( 1, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( -1, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( 0, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( '1', $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( '-1', $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_positive_number( '0', $setting ) );
		$this->assertSame( 'a', Sanitize::sanitize_positive_number( '', $setting ) );
		$this->assertSame( 'a', Sanitize::sanitize_positive_number( 'aaa', $setting ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_number_empty() {
		$setting = New WP_Customize_Setting(
			'test',
			'test',
			[]
		);

		$this->assertSame( 1, Sanitize::sanitize_number( 1, $setting ) );
		$this->assertSame( -1, Sanitize::sanitize_number( -1, $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number( 0, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number( '1', $setting ) );
		$this->assertSame( -1, Sanitize::sanitize_number( '-1', $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number( '0', $setting ) );
		$this->assertSame( '', Sanitize::sanitize_number( '', $setting ) );
		$this->assertSame( '', Sanitize::sanitize_number( 'aaa', $setting ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_number_zero() {
		$setting = New WP_Customize_Setting(
			'test',
			'test',
			[
				'default' => 0,
			]
		);

		$this->assertSame( 1, Sanitize::sanitize_number( 1, $setting ) );
		$this->assertSame( -1, Sanitize::sanitize_number( -1, $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number( 0, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number( '1', $setting ) );
		$this->assertSame( -1, Sanitize::sanitize_number( '-1', $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number( '0', $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number( '', $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number( 'aaa', $setting ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_number_string() {
		$setting = New WP_Customize_Setting(
			'test',
			'test',
			[
				'default' => 'a',
			]
		);

		$this->assertSame( 1, Sanitize::sanitize_number( 1, $setting ) );
		$this->assertSame( -1, Sanitize::sanitize_number( -1, $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number( 0, $setting ) );
		$this->assertSame( 1, Sanitize::sanitize_number( '1', $setting ) );
		$this->assertSame( -1, Sanitize::sanitize_number( '-1', $setting ) );
		$this->assertSame( 0, Sanitize::sanitize_number( '0', $setting ) );
		$this->assertSame( 'a', Sanitize::sanitize_number( '', $setting ) );
		$this->assertSame( 'a', Sanitize::sanitize_number( 'aaa', $setting ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_select() {
		$manager = New WP_Customize_Manager();

		$manager->add_control(
			'test',
			[
				'type'    => 'select',
				'choices' => [
					'aaa'   => '123',
					'bbb'   => '456',
					'ccc'   => '789',
				],
			]
		);

		$setting = New WP_Customize_Setting(
			$manager,
			'test',
			[
				'default' => 'ddd',
			]
		);

		$this->assertSame( 'aaa', Sanitize::sanitize_select( 'aaa', $setting ) );
		$this->assertSame( 'bbb', Sanitize::sanitize_select( 'bbb', $setting ) );
		$this->assertSame( 'ccc', Sanitize::sanitize_select( 'ccc', $setting ) );
		$this->assertSame( 'ddd', Sanitize::sanitize_select( 'eee', $setting ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_select_for_sanitize_key() {
		$manager = New WP_Customize_Manager();

		$manager->add_control(
			'test',
			[
				'type'    => 'select',
				'choices' => [
					'0.1'   => '123',
					'0.2'   => '456',
					'0.3'   => '789',
				],
			]
		);

		$setting = New WP_Customize_Setting(
			$manager,
			'test',
			[
				'default' => '0.1',
			]
		);

		$this->assertSame( '0.1', Sanitize::sanitize_select( '0.1', $setting ) );
		$this->assertSame( '0.2', Sanitize::sanitize_select( '0.2', $setting ) );
		$this->assertSame( '0.3', Sanitize::sanitize_select( '0.3', $setting ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_key() {
		$this->assertSame( '0.1', Sanitize::sanitize_key( '01', '0.1' ) );
		$this->assertSame( '0.2', Sanitize::sanitize_key( '02', '0.2' ) );
		$this->assertSame( '0.3', Sanitize::sanitize_key( '03', '0.3' ) );
	}

	/**
	 * @test
	 * @group Customizer_Sanitize
	 */
	public function sanitize_radio() {
		$manager = New WP_Customize_Manager();

		$manager->add_control(
			'test',
			[
				'type'    => 'radio',
				'choices' => [
					'aaa'   => '123',
					'bbb'   => '456',
					'ccc'   => '789',
				],
			]
		);

		$setting = New WP_Customize_Setting(
			$manager,
			'test',
			[
				'default' => 'ddd',
			]
		);

		$this->assertSame( 'aaa', Sanitize::sanitize_radio( 'aaa', $setting ) );
		$this->assertSame( 'bbb', Sanitize::sanitize_radio( 'bbb', $setting ) );
		$this->assertSame( 'ccc', Sanitize::sanitize_radio( 'ccc', $setting ) );
		$this->assertSame( 'ddd', Sanitize::sanitize_radio( 'eee', $setting ) );
	}

}
