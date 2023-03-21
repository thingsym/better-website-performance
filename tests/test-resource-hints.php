<?php
/**
 * Class Test_Resource_Hints
 *
 * @package Webby_Performance
 */

/**
 * Basic test case.
 */
class Test_Resource_Hints extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->resource_hints = new \Webby_Performance\Resource_Hints\Resource_Hints();
	}

	/**
	 * @test
	 * @group Resource_Hints
	 */
	function public_variable() {
		$this->assertSame( 'webby_performance_resource_hints', $this->resource_hints->section_id );
		$this->assertSame( 180, $this->resource_hints->section_priority );
		$this->assertSame( 'webby_performance_resource_hints_options', $this->resource_hints->options_name );
		$this->assertSame( 'option', $this->resource_hints->type );
		$this->assertSame( 'manage_options', $this->resource_hints->capability );

		$expected = [
			'dns_prefetch' => '',
			'preconnect'   => '',
			'prefetch'     => '',
			'prerender'    => '',
		];

		$this->assertSame( $expected, $this->resource_hints->default_options );
	}

	/**
	 * @test
	 * @group Resource_Hints
	 */
	function constructor() {
		$this->assertSame( 10, has_filter( 'customize_register', [ $this->resource_hints, 'customizer' ] ) );
		$this->assertSame( 2, has_action( 'wp_head', [ $this->resource_hints, 'print_tag' ] ) );
		$this->assertSame( 10, has_action( 'wp_resource_hints', [ $this->resource_hints, 'add_resource_hints' ] ) );
	}

	/**
	 * @test
	 * @group Resource_Hints
	 */
	function get_options_default() {
		$options = $this->resource_hints->get_options();
		$expected = [
			'dns_prefetch' => '',
			'preconnect'   => '',
			'prefetch'     => '',
			'prerender'    => '',
		];

		$this->assertSame( $expected, $options );
	}

	/**
	 * @test
	 * @group Resource_Hints
	 */
	function get_options_case_1() {
		$expected = [
			'dns_prefetch' => 'www.google.com',
			'preconnect'   => 'bbbbbb',
			'prefetch'     => 'ccccc, script, crossorigin',
			'prerender'    => 'dddddd',
		];

		$options = [
			'dns_prefetch' => 'www.google.com',
			'preconnect'   => 'bbbbbb',
			'prefetch'     => 'ccccc, script, crossorigin',
			'prerender'    => 'dddddd',
		];

		update_option( 'webby_performance_resource_hints_options', $options );

		$actual = $this->resource_hints->get_options();

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Resource_Hints
	 */
	public function get_options_case_filters() {
		$options = [
			'dns_prefetch' => 'www.google.com',
			'preconnect'   => 'bbbbbb',
			'prefetch'     => 'ccccc, script, crossorigin',
			'prerender'    => 'dddddd',
		];

		update_option( 'webby_performance_resource_hints_options', $options );

		add_filter( 'webby_performance/resource_hints/get_options', [ $this, '_filter_options' ], 10 );

		$expected = [
			'dns_prefetch' => 'aaa',
			'preconnect'   => 'bbb',
			'prefetch'     => 'ccc',
			'prerender'    => 'ddd',
		];

		$actual = $this->resource_hints->get_options();
		$this->assertSame( $expected, $actual );

		add_filter( 'webby_performance/resource_hints/get_option', [ $this, '_filter_option' ], 10, 2 );

		$actual = $this->resource_hints->get_options( 'dns_prefetch' );
		$this->assertSame( 'xyz', $actual );
	}

	public function _filter_options( $options ) {
		$expected = [
			'dns_prefetch' => 'www.google.com',
			'preconnect'   => 'bbbbbb',
			'prefetch'     => 'ccccc, script, crossorigin',
			'prerender'    => 'dddddd',
		];

		$this->assertSame( $expected, $options );

		$options = [
			'dns_prefetch' => 'aaa',
			'preconnect'   => 'bbb',
			'prefetch'     => 'ccc',
			'prerender'    => 'ddd',
		];

		return $options;
	}

	public function _filter_option( $option, $name ) {
		$expected = 'www.google.com';

		$this->assertSame( $expected, $option );
		$this->assertSame( $name, 'dns_prefetch' );

		$option = 'xyz';

		return $option;
	}

	/**
	 * @test
	 * @group Resource_Hints
	 */
	function add_resource_hints() {
		$actual = $this->resource_hints->add_resource_hints( [], 'dns-prefetch' );

		$this->assertIsArray( $actual );
		$this->assertEmpty( $actual );

		$expected = [
			[
				'href' => 'www.google.com',
			],
		];

		$options = [
			'dns_prefetch' => 'www.google.com',
			'preconnect'   => 'bbbbbb',
			'prefetch'     => 'ccccc, script, crossorigin',
			'prerender'    => 'dddddd',
		];

		update_option( 'webby_performance_resource_hints_options', $options );

		$actual = $this->resource_hints->add_resource_hints( [], 'dns-prefetch' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$expected = [
			[
				'href' => 'bbbbbb',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( [], 'preconnect' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$expected = [
			[
				'href'        => 'ccccc',
				'as'          => 'script',
				'crossorigin' => 'crossorigin',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( [], 'prefetch' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$expected = [
			[
				'href' => 'dddddd',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( [], 'prerender' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$options = [
			'dns_prefetch' => 'www.google.com',
			'preconnect'   => 'bbbbbb',
			'prefetch'     => 'ccccc, script',
			'prerender'    => 'dddddd',
		];

		update_option( 'webby_performance_resource_hints_options', $options );

		$expected = [
			[
				'href' => 'ccccc',
				'as'   => 'script',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( [], 'prefetch' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$options = [
			'dns_prefetch' => 'www.google.com',
			'preconnect'   => 'bbbbbb',
			'prefetch'     => 'ccccc',
			'prerender'    => 'dddddd',
		];

		update_option( 'webby_performance_resource_hints_options', $options );

		$expected = [
			[
				'href' => 'ccccc',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( [], 'prefetch' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

	}

	/**
	 * @test
	 * @group Resource_Hints
	 */
	function add_resource_hints_multiline() {

		$expected = [
			[
				'href' => 'www.google.com',
			],
			[
				'href' => 'example.com',
			],
		];

		$options = [
			'dns_prefetch' => 'www.google.com
example.com',
			'preconnect'   => 'bbbbbb
eeee',
			'prefetch'     => 'ccccc, script, crossorigin
ffffff',
			'prerender'    => 'dddddd
ggggg',
		];

		update_option( 'webby_performance_resource_hints_options', $options );

		$actual = $this->resource_hints->add_resource_hints( [], 'dns-prefetch' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$expected = [
			[
				'href' => 'bbbbbb',
			],
			[
				'href' => 'eeee',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( [], 'preconnect' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$expected = [
			[
				'href'        => 'ccccc',
				'as'          => 'script',
				'crossorigin' => 'crossorigin',
			],
			[
				'href' => 'ffffff',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( [], 'prefetch' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$expected = [
			[
				'href' => 'dddddd',
			],
			[
				'href' => 'ggggg',
			],
		];

	}

	/**
	 * @test
	 * @group Resource_Hints
	 */
	function add_resource_hints_existed() {
		$existed = [
			[
				'href' => 'example.com',
			],
		];

		$expected = [
			[
				'href' => 'example.com',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( $existed, 'dns-prefetch' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$expected = [
			[
				'href' => 'example.com',
			],
			[
				'href' => 'www.google.com',
			],
		];

		$options = [
			'dns_prefetch' => 'www.google.com',
			'preconnect'   => 'bbbbbb',
			'prefetch'     => 'ccccc, script, crossorigin',
			'prerender'    => 'dddddd',
		];

		update_option( 'webby_performance_resource_hints_options', $options );

		$actual = $this->resource_hints->add_resource_hints( $existed, 'dns-prefetch' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$expected = [
			[
				'href' => 'example.com',
			],
			[
				'href' => 'bbbbbb',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( $existed, 'preconnect' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$expected = [
			[
				'href' => 'example.com',
			],
			[
				'href'        => 'ccccc',
				'as'          => 'script',
				'crossorigin' => 'crossorigin',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( $existed, 'prefetch' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$expected = [
			[
				'href' => 'example.com',
			],
			[
				'href' => 'dddddd',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( $existed, 'prerender' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$options = [
			'dns_prefetch' => 'www.google.com',
			'preconnect'   => 'bbbbbb',
			'prefetch'     => 'ccccc, script',
			'prerender'    => 'dddddd',
		];

		update_option( 'webby_performance_resource_hints_options', $options );

		$expected = [
			[
				'href' => 'example.com',
			],
			[
				'href' => 'ccccc',
				'as'   => 'script',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( $existed, 'prefetch' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

		$options = [
			'dns_prefetch' => 'www.google.com',
			'preconnect'   => 'bbbbbb',
			'prefetch'     => 'ccccc',
			'prerender'    => 'dddddd',
		];

		update_option( 'webby_performance_resource_hints_options', $options );

		$expected = [
			[
				'href' => 'example.com',
			],
			[
				'href' => 'ccccc',
			],
		];

		$actual = $this->resource_hints->add_resource_hints( $existed, 'prefetch' );

		$this->assertIsArray( $actual );
		$this->assertSame( $expected, $actual );

	}

	/**
	 * @test
	 * @group Resource_Hints
	 */
	public function print_tag() {
		ob_start();
		$this->resource_hints->print_tag();
		$actual = ob_get_clean();

		$this->assertMatchesRegularExpression( '#<meta http-equiv="x-dns-prefetch-control" content="on">#', $actual );
	}

}
