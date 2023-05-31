<?php
/**
 * Class Test_Preload
 *
 * @package Better_Website_Performance
 */

/**
 * Basic test case.
 */
class Test_Preload extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->preload = new \Better_Website_Performance\Preload\Preload();

		$this->preload_existed = [
			[
				'href' => '//example.com/',
				'as'   => 'font',
				'type' => 'text/html',
			],
		];
	}

	public function tearDown(): void {
		delete_option( $this->preload->options_name );
		remove_filter( 'better_website_performance/preload/get_option', array( $this, '_filter_option' ) );
		remove_filter( 'better_website_performance/preload/get_options', array( $this, '_filter_options' ) );
		parent::tearDown();
	}

	/**
	 * @test
	 * @group Preload
	 */
	function public_variable() {
		$this->assertSame( 'better_website_performance_preload', $this->preload->section_id );
		$this->assertSame( 180, $this->preload->section_priority );
		$this->assertSame( 'better_website_performance_preload_options', $this->preload->options_name );
		$this->assertSame( 'option', $this->preload->type );
		$this->assertSame( 'manage_options', $this->preload->capability );

		$expected = [
			'preload' => '',
		];
		$this->assertSame( $expected, $this->preload->default_options );
	}

	/**
	 * @test
	 * @group Preload
	 */
	function constructor() {
		$this->assertSame( 10, has_filter( 'customize_register', [ $this->preload, 'customizer' ] ) );
		$this->assertSame( 10, has_action( 'wp_preload_resources', [ $this->preload, 'add_preload' ] ) );
	}

	/**
	 * @test
	 * @group Preload
	 */
	function get_options_default() {
		$actual = $this->preload->get_options();
		$expected = [
			'preload' => '',
		];

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Preload
	 */
	function get_options_case_1() {
		$expected = [
			'preload' => '//fonts.googleapis.com/css?family=Montserrat, font, , text/html',
		];

		$options = [
			'preload' => '//fonts.googleapis.com/css?family=Montserrat, font, , text/html',
		];

		update_option( $this->preload->options_name, $options );

		$actual = $this->preload->get_options();

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Preload
	 */
	public function get_options_case_filters() {
		$options = [
			'preload' => '//fonts.googleapis.com/css?family=Montserrat, font, , text/html',
		];

		update_option( $this->preload->options_name, $options );

		add_filter( 'better_website_performance/preload/get_options', [ $this, '_filter_options' ], 10 );

		$expected = [
			'preload' => 'abc',
		];

		$actual = $this->preload->get_options();
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Preload
	 */
	public function get_option_case_filters() {
		$options = [
			'preload' => '//fonts.googleapis.com/css?family=Montserrat, font, , text/html',
		];

		update_option( $this->preload->options_name, $options );

		add_filter( 'better_website_performance/preload/get_option', [ $this, '_filter_option' ], 10, 2 );

		$actual = $this->preload->get_options( 'preload' );
		$this->assertSame( 'xyz', $actual );
	}

	public function _filter_options( $options ) {
		$expected = [
			'preload' => '//fonts.googleapis.com/css?family=Montserrat, font, , text/html',
		];

		$this->assertSame( $expected, $options );

		$options = [
			'preload' => 'abc',
		];

		return $options;
	}

	public function _filter_option( $option, $name ) {
		$expected = '//fonts.googleapis.com/css?family=Montserrat, font, , text/html';

		$this->assertSame( $expected, $option );
		$this->assertSame( $name, 'preload' );

		$option = 'xyz';

		return $option;
	}

	/**
	 * @test
	 * @group Preload
	 */
	function add_preload_default() {
		$actual = $this->preload->add_preload( [] );

		$this->assertIsArray( $actual );
		$this->assertEmpty( $actual );
	}

	/**
	 * @test
	 * @group Preload
	 */
	function add_preload_case_1() {
		$expected = [
			[
				'href' => '//fonts.googleapis.com/css?family=Montserrat',
				'as'   => 'font',
				'type' => 'text/html',
			],
		];

		$options = [
			'preload' => '//fonts.googleapis.com/css?family=Montserrat, font, , text/html',
		];

		update_option( $this->preload->options_name, $options );

		$actual = $this->preload->add_preload( [] );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Preload
	 */
	function add_preload_case_2() {

		$expected = [
			[
				'href'        => '//fonts.googleapis.com/css?family=Montserrat',
				'as'          => 'font',
				'crossorigin' => 'crossorigin',
				'type'        => 'text/html',
			],
		];

		$options = [
			'preload' => '//fonts.googleapis.com/css?family=Montserrat, font, crossorigin, text/html',
		];

		update_option( $this->preload->options_name, $options );

		$actual = $this->preload->add_preload( [] );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Preload
	 */
	function add_preload_case_3() {

		$expected = [
			[
				'href' => '//fonts.googleapis.com/css?family=Montserrat',
				'as'   => 'font',
			],
		];

		$options = [
			'preload' => '//fonts.googleapis.com/css?family=Montserrat, font',
		];

		update_option( $this->preload->options_name, $options );

		$actual = $this->preload->add_preload( [] );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Preload
	 */
	function add_preload_case_4() {

		$expected = [
			[
				'href' => '//fonts.googleapis.com/css?family=Montserrat',
			],
		];

		$options = [
			'preload' => '//fonts.googleapis.com/css?family=Montserrat',
		];

		update_option( $this->preload->options_name, $options );

		$actual = $this->preload->add_preload( [] );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Preload
	 */
	function add_preload_multiline() {
		$expected = [
			[
				'href' => '//fonts.googleapis.com/css?family=Montserrat',
				'as'   => 'font',
				'type' => 'text/html',
			],
			[
				'href'        => '//fonts.googleapis.com/css?family=Montserrat',
				'as'          => 'font',
				'crossorigin' => 'crossorigin',
				'type'        => 'text/html',
			],
			[
				'href' => '//fonts.googleapis.com/css?family=Montserrat',
				'as'   => 'font',
			],
			[
				'href' => '//fonts.googleapis.com/css?family=Montserrat',
			],
		];

		$options = [
			'preload' => '//fonts.googleapis.com/css?family=Montserrat, font, , text/html
//fonts.googleapis.com/css?family=Montserrat, font, crossorigin , text/html
//fonts.googleapis.com/css?family=Montserrat, font
//fonts.googleapis.com/css?family=Montserrat',
		];

		update_option( $this->preload->options_name, $options );

		$actual = $this->preload->add_preload( [] );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

	}

	/**
	 * @test
	 * @group Preload
	 */
	function add_preload_existed_default() {
		$this->preload_existed = [
			[
				'href' => '//example.com/',
				'as'   => 'font',
				'type' => 'text/html',
			],
		];

		$expected = [
			[
				'href' => '//example.com/',
				'as'   => 'font',
				'type' => 'text/html',
			],
		];

		$actual = $this->preload->add_preload( $this->preload_existed );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

	}

	/**
	 * @test
	 * @group Preload
	 */
	function add_preload_existed_case_1() {
		$expected = [
			[
				'href' => '//example.com/',
				'as'   => 'font',
				'type' => 'text/html',
			],
			[
				'href' => '//fonts.googleapis.com/css?family=Montserrat',
				'as'   => 'font',
				'type' => 'text/html',
			],
			[
				'href'        => '//fonts.googleapis.com/css?family=Montserrat',
				'as'          => 'font',
				'crossorigin' => 'crossorigin',
				'type'        => 'text/html',
			],
			[
				'href' => '//fonts.googleapis.com/css?family=Montserrat',
				'as'   => 'font',
			],
			[
				'href' => '//fonts.googleapis.com/css?family=Montserrat',
			],
		];

		$options = [
			'preload' => '//fonts.googleapis.com/css?family=Montserrat, font, , text/html
//fonts.googleapis.com/css?family=Montserrat, font, crossorigin , text/html
//fonts.googleapis.com/css?family=Montserrat, font
//fonts.googleapis.com/css?family=Montserrat',
		];

		update_option( $this->preload->options_name, $options );

		$actual = $this->preload->add_preload( $this->preload_existed );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

	}

}
