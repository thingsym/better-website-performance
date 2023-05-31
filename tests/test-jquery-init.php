<?php
/**
 * Class Test_Jquery_Init
 *
 * @package Better_Website_Performance
 */

/**
 * Basic test case.
 */
class Test_Jquery_Init extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->jquery = new \Better_Website_Performance\Jquery\Jquery();
	}

	public function tearDown(): void {
		delete_option( $this->jquery->options_name );
		remove_filter( 'better_website_performance/jquery/get_option', array( $this, '_filter_option' ) );
		remove_filter( 'better_website_performance/jquery/get_options', array( $this, '_filter_options' ) );

		global $wp_scripts;
		wp_default_scripts( $wp_scripts );
		parent::tearDown();
	}

	/**
	 * @test
	 * @group Jquery
	 */
	function init_default() {
		$this->jquery->init();

		global $wp_scripts;
		$jquery         = $wp_scripts->registered['jquery'];
		$jquery_core    = $wp_scripts->registered['jquery-core'];
		$jquery_migrate = $wp_scripts->registered['jquery-migrate'];

		$this->assertSame( 'jquery', $jquery->handle );
		$this->assertTrue( in_array( 'jquery-core', $jquery->deps, false ) );
		$this->assertTrue( in_array( 'jquery-migrate', $jquery->deps, false ) );
		$this->assertSame( 'jquery-core', $jquery_core->handle );
		$this->assertSame( 'jquery-migrate', $jquery_migrate->handle );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	function init_disabled_jquery() {
		$options = array(
			'jquery'         => false,
			'jquery_migrate' => true,
			'in_footer'      => false,
		);

		update_option( $this->jquery->options_name, $options );

		$this->jquery->init();

		global $wp_scripts;

		$this->assertFalse( isset( $wp_scripts->registered['jquery'] ) );
		$this->assertFalse( isset( $wp_scripts->registered['jquery-core'] ) );
		$this->assertFalse( isset( $wp_scripts->registered['jquery-migrate'] ) );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	function init_disabled_migrate() {
		$options = array(
			'jquery'         => true,
			'jquery_migrate' => false,
			'in_footer'      => false,
		);

		update_option( $this->jquery->options_name, $options );

		$this->jquery->init();

		global $wp_scripts;
		$jquery      = $wp_scripts->registered['jquery'];
		$jquery_core = $wp_scripts->registered['jquery-core'];
		$this->assertFalse( isset( $wp_scripts->registered['jquery-migrate'] ) );

		$this->assertSame( 'jquery', $jquery->handle );
		$this->assertTrue( in_array( 'jquery-core', $jquery->deps, false ) );
		$this->assertFalse( in_array( 'jquery-migrate', $jquery->deps, false ) );
		$this->assertSame( 'jquery-core', $jquery_core->handle );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	function init_in_footer() {
		$options = array(
			'jquery'         => true,
			'jquery_migrate' => true,
			'in_footer'      => true,
		);

		update_option( $this->jquery->options_name, $options );

		$this->jquery->init();

		global $wp_scripts;
		$jquery         = $wp_scripts->registered['jquery'];
		$jquery_core    = $wp_scripts->registered['jquery-core'];
		$jquery_migrate = $wp_scripts->registered['jquery-migrate'];

		$this->assertSame( 'jquery', $jquery->handle );
		$this->assertTrue( in_array( 'jquery-core', $jquery->deps, false ) );
		$this->assertTrue( in_array( 'jquery-migrate', $jquery->deps, false ) );
		$this->assertSame( 'jquery-core', $jquery_core->handle );
		$this->assertSame( 'jquery-migrate', $jquery_migrate->handle );

		$this->assertSame( 1, $jquery->extra['group'] );
		$this->assertSame( 1, $jquery_core->extra['group'] );
		$this->assertSame( 1, $jquery_migrate->extra['group'] );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	function init_disabled_migrate_in_footer() {
		$options = array(
			'jquery'         => true,
			'jquery_migrate' => false,
			'in_footer'      => true,
		);

		update_option( $this->jquery->options_name, $options );

		$this->jquery->init();

		global $wp_scripts;
		$jquery      = $wp_scripts->registered['jquery'];
		$jquery_core = $wp_scripts->registered['jquery-core'];
		$this->assertFalse( isset( $wp_scripts->registered['jquery-migrate'] ) );

		$this->assertSame( 'jquery', $jquery->handle );
		$this->assertTrue( in_array( 'jquery-core', $jquery->deps, false ) );
		$this->assertFalse( in_array( 'jquery-migrate', $jquery->deps, false ) );
		$this->assertSame( 'jquery-core', $jquery_core->handle );

		$this->assertSame( 1, $jquery->extra['group'] );
		$this->assertSame( 1, $jquery_core->extra['group'] );
	}

}
