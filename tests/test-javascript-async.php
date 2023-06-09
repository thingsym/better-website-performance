<?php
/**
 * Class Test_Javascript_Async
 *
 * @package Better_Website_Performance
 */

/**
 * Basic test case.
 */
class Test_Javascript_Async extends WP_UnitTestCase {
	public $javascript_async;

	public function setUp(): void {
		parent::setUp();
		$this->javascript_async = new \Better_Website_Performance\JavaScript\Async();
	}

	public function tearDown(): void {
		delete_option( $this->javascript_async->options_name );
		remove_filter( 'better_website_performance/async_javascript/get_option', array( $this, '_filter_option' ) );
		remove_filter( 'better_website_performance/async_javascript/get_options', array( $this, '_filter_options' ) );
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	function public_variable() {
		$this->assertSame( 'better_website_performance_async_javascript', $this->javascript_async->section_id );
		$this->assertSame( 160, $this->javascript_async->section_priority );
		$this->assertSame( 'better_website_performance_async_javascript_options', $this->javascript_async->options_name );
		$this->assertSame( 'option', $this->javascript_async->type );
		$this->assertSame( 'manage_options', $this->javascript_async->capability );

		$expected = [
			'async' => '',
			'exclude' => '',
		];
		$this->assertSame( $expected, $this->javascript_async->default_options );

		$this->assertSame( [], $this->javascript_async->exclude_handles );
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	function constructor() {
		$this->assertSame( 10, has_filter( 'customize_register', [ $this->javascript_async, 'customizer' ] ) );
		$this->assertSame( 10, has_action( 'script_loader_tag', [ $this->javascript_async, 'async_script_tag' ] ) );
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	function get_options_default() {
		$expected = [
			'async' => '',
			'exclude' => '',
		];

		$actual = $this->javascript_async->get_options();

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	function get_options_case_1() {
		$expected = [
			'async' => 'async',
			'exclude' => '',
		];

		$options = array(
			'async' => 'async',
			'exclude' => '',
		);

		update_option( $this->javascript_async->options_name, $options );

		$actual = $this->javascript_async->get_options();

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	public function get_options_case_filters() {
		$expected = array(
			'async' => 'async',
			'exclude' => '',
		);

		add_filter( 'better_website_performance/async_javascript/get_options', array( $this, '_filter_options' ), 10 );

		$actual = $this->javascript_async->get_options();
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	public function get_option_case_filters() {
		add_filter( 'better_website_performance/async_javascript/get_option', array( $this, '_filter_option' ), 10, 2 );

		$actual = $this->javascript_async->get_options( 'async' );
		$this->assertSame( 'async', $actual );
	}

	public function _filter_options( $options ) {
		$expected = array(
			'async' => '',
			'exclude' => '',
		);
		$this->assertSame( $expected, $options );

		$options = array(
			'async' => 'async',
			'exclude' => '',
		);

		return $options;
	}

	public function _filter_option( $option, $name ) {
		$this->assertEmpty( $option );
		$this->assertSame( $name, 'async' );

		$option = 'async';

		return $option;
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	function async_script_tag() {
		$tag = "<script src='test.min.js?ver=3.1.2' id='wp-test-js'>";
		$src = "test.min.js?ver=3.1.2";

		$actual = $this->javascript_async->async_script_tag( $tag, 'test-js', $src );

		$this->assertMatchesRegularExpression( '#<script src=#', $actual );

		$options = array(
			'async' => 'async',
			'exclude' => '',
		);

		update_option( $this->javascript_async->options_name, $options );

		$actual = $this->javascript_async->async_script_tag( $tag, 'test-js', $src );

		$this->assertMatchesRegularExpression( '#<script async=\'async\' src=#', $actual );

		$options = array(
			'async' => 'defer',
			'exclude' => '',
		);

		update_option( $this->javascript_async->options_name, $options );

		$actual = $this->javascript_async->async_script_tag( $tag, 'test-js', $src );

		$this->assertMatchesRegularExpression( '#<script defer=\'defer\' src=#', $actual );

	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	function async_script_tag_exclude() {
		$tag = "<script src='test.min.js?ver=3.1.2' id='wp-test-js'>";
		$src = "test.min.js?ver=3.1.2";

		$options = array(
			'async'   => 'async',
			'exclude' => 'test-js',
		);

		update_option( $this->javascript_async->options_name, $options );

		$this->javascript_async->setup_exclude_script();
		$actual = $this->javascript_async->async_script_tag( $tag, 'test-js', $src );

		$this->assertMatchesRegularExpression( '#<script src=#', $actual );
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	function validate_exclude_script() {
		$this->javascript_async->exclude_handles[] = 'test-js';
		$this->assertTrue( $this->javascript_async->validate_exclude_script( 'test-js' ) );
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	function setup_exclude_script() {
		$options = array(
			'async'   => 'defer',
			'exclude' => 'test1-js
test2-js
test3-js',
		);
		update_option( $this->javascript_async->options_name, $options );

		$this->javascript_async->setup_exclude_script();

		$this->assertTrue( in_array( 'test1-js', $this->javascript_async->exclude_handles ) );
		$this->assertTrue( in_array( 'test2-js', $this->javascript_async->exclude_handles ) );
		$this->assertTrue( in_array( 'test3-js', $this->javascript_async->exclude_handles ) );
	}

}
