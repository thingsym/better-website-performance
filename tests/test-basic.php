<?php
/**
 * Class Test_Webby_Performance_Basic
 *
 * @package Webby_Performance
 */

/**
 * Basic test case.
 */
class Test_Webby_Performance_Basic extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->webby_performance = new \Webby_Performance\Webby_Performance();
	}

	public function tearDown(): void {
		remove_filter( 'load_textdomain_mofile', [ $this, '_change_textdomain_mofile' ] );
		remove_filter( 'locale', [ $this, '_change_locale' ] );
		unload_textdomain( 'webby-performance' );
	}

	/**
	 * @test
	 * @group basic
	 */
	function public_variable() {
		$this->assertIsArray( $this->webby_performance->plugin_data );
		$this->assertEmpty( $this->webby_performance->plugin_data );
	}

	/**
	 * @test
	 * @group basic
	 */
	function basic() {
		$this->assertMatchesRegularExpression( '#/webby-performance/webby-performance.php$#', WEBBY_PERFORMANCE );
		$this->assertTrue( class_exists( '\Webby_Performance\Webby_Performance' ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	function constructor() {
		$this->assertSame( 10, has_filter( 'plugins_loaded', [ $this->webby_performance, 'load_plugin_data' ] ) );

		$this->assertSame( 10, has_action( 'plugins_loaded', [ $this->webby_performance, 'init' ] ) );
		$this->assertSame( 10, has_action( 'plugins_loaded', [ $this->webby_performance, 'load_class_functions' ] ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	function init() {
		$this->webby_performance->init();

		$this->assertSame( 10, has_action( 'init', [ $this->webby_performance, 'load_textdomain' ] ) );
		$this->assertSame( 10, has_filter( 'plugin_row_meta', [ $this->webby_performance, 'plugin_metadata_links' ] ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function load_textdomain() {
		$loaded = $this->webby_performance->load_textdomain();
		$this->assertFalse( $loaded );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function load_textdomain_mofile() {
		add_filter( 'locale', [ $this, '_change_locale' ] );
		add_filter( 'load_textdomain_mofile', [ $this, '_change_textdomain_mofile' ], 10, 2 );

		$loaded = $this->webby_performance->load_textdomain();
		$this->assertTrue( $loaded );
	}

	/**
	 * hook for load_textdomain
	 */
	function _change_locale( $locale ) {
		return 'ja';
	}

	function _change_textdomain_mofile( $mofile, $domain ) {
		if ( $domain === 'webby-performance' ) {
			$locale = determine_locale();
			$mofile = plugin_dir_path( WEBBY_PERFORMANCE ) . 'languages/webby-performance-' . $locale . '.mo';

			$this->assertSame( $locale, get_locale() );
			$this->assertFileExists( $mofile );
		}

		return $mofile;
	}

	/**
	 * @test
	 * @group basic
	 */
	public function load_class_functions() {
		$this->webby_performance->load_class_functions();

		$this->assertTrue( class_exists( '\Webby_Performance\Customizer\Panel' ) );
		$this->assertTrue( class_exists( '\Webby_Performance\Customizer\Sanitize' ) );

		$this->assertTrue( class_exists( '\Webby_Performance\Wp_Custom_Css\Wp_Custom_Css' ) );

		$this->assertTrue( class_exists( '\Webby_Performance\Wp_Head\Wp_Head' ) );
		$this->assertTrue( class_exists( '\Webby_Performance\Resource_Hints\Resource_Hints' ) );
		$this->assertTrue( class_exists( '\Webby_Performance\Preload\Preload' ) );
		$this->assertTrue( class_exists( '\Webby_Performance\Emoji\Emoji' ) );
		$this->assertTrue( class_exists( '\Webby_Performance\Image_Srcset\Image_Srcset' ) );
		$this->assertTrue( class_exists( '\Webby_Performance\Jquery\Jquery' ) );

	}

	/**
	 * @test
	 * @group basic
	 */
	public function plugin_metadata_links() {
		$links = $this->webby_performance->plugin_metadata_links( [], plugin_basename( WEBBY_PERFORMANCE ) );
		$this->assertContains( '<a href="https://github.com/sponsors/thingsym">Become a sponsor</a>', $links );
	}

	/**
	 * @test
	 * @group basic
	 */
	function uninstall() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
