<?php
/**
 * Class Test_Jquery
 *
 * @package Better_Website_Performance
 */

/**
 * Basic test case.
 */
class Test_Jquery extends WP_UnitTestCase {
	public $jquery;

	public function setUp(): void {
		parent::setUp();
		$this->jquery = new \Better_Website_Performance\Jquery\Jquery();
	}

	public function tearDown(): void {
		delete_option( $this->jquery->options_name );
		remove_filter( 'better_website_performance/jquery/get_option', array( $this, '_filter_option' ) );
		remove_filter( 'better_website_performance/jquery/get_options', array( $this, '_filter_options' ) );
		parent::tearDown();
	}

	/**
	 * @test
	 * @group Jquery
	 */
	function public_variable() {
		$this->assertSame( 'better_website_performance_jquery', $this->jquery->section_id );
		$this->assertSame( 160, $this->jquery->section_priority );
		$this->assertSame( 'better_website_performance_jquery_options', $this->jquery->options_name );
		$this->assertSame( 'option', $this->jquery->type );
		$this->assertSame( 'manage_options', $this->jquery->capability );

		$expected = [
			'jquery'         => true,
			'jquery_migrate' => true,
			'in_footer'      => false,
		];
		$this->assertSame( $expected, $this->jquery->default_options );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	function constructor() {
		$this->assertSame( 10, has_filter( 'customize_register', [ $this->jquery, 'customizer' ] ) );
		$this->assertSame( 10, has_action( 'init', [ $this->jquery, 'init' ] ) );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	function get_options_default() {
		$expected = [
			'jquery'         => true,
			'jquery_migrate' => true,
			'in_footer'      => false,
		];

		$actual = $this->jquery->get_options();

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	function get_options_case_1() {
		$expected = [
			'jquery'         => false,
			'jquery_migrate' => false,
			'in_footer'      => true,
		];

		$options = array(
			'jquery'         => false,
			'jquery_migrate' => false,
			'in_footer'      => true,
		);

		update_option( $this->jquery->options_name, $options );

		$actual = $this->jquery->get_options();

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	public function get_options_case_filters() {
		$expected = array(
			'jquery'         => false,
			'jquery_migrate' => false,
			'in_footer'      => true,
		);

		add_filter( 'better_website_performance/jquery/get_options', array( $this, '_filter_options' ), 10 );

		$actual = $this->jquery->get_options();
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	public function get_option_case_filters() {
		add_filter( 'better_website_performance/jquery/get_option', array( $this, '_filter_option' ), 10, 2 );

		$actual = $this->jquery->get_options( 'jquery' );
		$this->assertFalse( $actual );
	}

	public function _filter_options( $options ) {
		$expected = array(
			'jquery'         => true,
			'jquery_migrate' => true,
			'in_footer'      => false,
		);
		$this->assertSame( $expected, $options );

		$options = array(
			'jquery'         => false,
			'jquery_migrate' => false,
			'in_footer'      => true,
		);

		return $options;
	}

	public function _filter_option( $option, $name ) {
		$this->assertTrue( $option );
		$this->assertSame( $name, 'jquery' );

		$option = false;

		return $option;
	}

}
