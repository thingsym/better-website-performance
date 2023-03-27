<?php
/**
 * Class Test_Image_Srcset
 *
 * @package Webby_Performance
 */

/**
 * Basic test case.
 */
class Test_Image_Srcset extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->image_srcset = new \Webby_Performance\Image_Srcset\Image_Srcset();
	}

	public function tearDown(): void {
		delete_option( $this->image_srcset->options_name );
		remove_filter( 'webby_performance/image_srcset/get_option', array( $this, '_filter_option' ) );
		remove_filter( 'webby_performance/image_srcset/get_options', array( $this, '_filter_options' ) );
	}

	/**
	 * @test
	 * @group Image_Srcset
	 */
	function public_variable() {
		$this->assertSame( 'webby_performance_image_srcset', $this->image_srcset->section_id );
		$this->assertSame( 160, $this->image_srcset->section_priority );
		$this->assertSame( $this->image_srcset->options_name, $this->image_srcset->options_name );
		$this->assertSame( 'option', $this->image_srcset->type );
		$this->assertSame( 'manage_options', $this->image_srcset->capability );

		$expected = array(
			'image_srcset' => true,
		);
		$this->assertSame( $expected, $this->image_srcset->default_options );
	}

	/**
	 * @test
	 * @group Image_Srcset
	 */
	function constructor() {
		$this->assertSame( 10, has_filter( 'customize_register', [ $this->image_srcset, 'customizer' ] ) );
		$this->assertSame( 10, has_action( 'init', [ $this->image_srcset, 'init' ] ) );
	}

	/**
	 * @test
	 * @group Image_Srcset
	 */
	function get_options_default() {
		$actual = $this->image_srcset->get_options();
		$expected = array(
			'image_srcset' => true,
		);

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Image_Srcset
	 */
	function get_options_case_1() {
		$options = array(
			'image_srcset' => false,
		);

		update_option( $this->image_srcset->options_name, $options );

		$actual = $this->image_srcset->get_options();

		$this->assertFalse( $actual['image_srcset'] );

	}

	/**
	 * @test
	 * @group Image_Srcset
	 */
	function get_options_case_2() {
		$options = array(
			'image_srcset' => true,
		);

		update_option( $this->image_srcset->options_name, $options );

		$actual = $this->image_srcset->get_options();

		$this->assertTrue( $actual['image_srcset'] );
	}

	/**
	 * @test
	 * @group Image_Srcset
	 */
	public function get_options_case_filters() {
		add_filter( 'webby_performance/image_srcset/get_options', array( $this, '_filter_options' ), 10 );

		$actual = $this->image_srcset->get_options();
		$this->assertFalse( $actual['image_srcset'] );

	}

	/**
	 * @test
	 * @group Image_Srcset
	 */
	public function get_option_case_filters() {
		add_filter( 'webby_performance/image_srcset/get_options', array( $this, '_filter_options' ), 10 );

		$actual = $this->image_srcset->get_options();
		$this->assertFalse( $actual['image_srcset'] );

		add_filter( 'webby_performance/image_srcset/get_option', array( $this, '_filter_option' ), 10, 2 );

		$actual = $this->image_srcset->get_options( 'image_srcset' );
		$this->assertFalse( $actual );
	}

	public function _filter_options( $options ) {
		$expected = array(
			'image_srcset' => true,
		);
		$this->assertSame( $expected, $options );

		$options = array(
			'image_srcset' => false,
		);

		return $options;
	}

	public function _filter_option( $option, $name ) {
		$this->assertTrue( $option );
		$this->assertSame( $name, 'image_srcset' );

		$option = false;
		return $option;
	}

	/**
	 * @test
	 * @group Image_Srcset
	 */
	function init_dafault() {
		$this->image_srcset->init();

		$this->assertFalse( has_filter( 'wp_calculate_image_srcset_meta', '__return_null' ) );
	}

	/**
	 * @test
	 * @group Image_Srcset
	 */
	function init_case_1() {

		$options = array(
			'image_srcset' => true,
		);

		update_option( $this->image_srcset->options_name, $options );

		$this->image_srcset->init();

		$this->assertFalse( has_filter( 'wp_calculate_image_srcset_meta', '__return_null' ) );
	}

	/**
	 * @test
	 * @group Image_Srcset
	 */
	function init_case_2() {
		$options = array(
			'image_srcset' => false,
		);

		update_option( $this->image_srcset->options_name, $options );

		$this->image_srcset->init();

		$this->assertSame( 10, has_filter( 'wp_calculate_image_srcset_meta', '__return_null' ) );
	}

}
