<?php
/**
 * Class Test_Better_Website_Performance_Basic
 *
 * @package Better_Website_Performance
 */

/**
 * Basic test case.
 */
class Test_Better_Website_Performance_Basic extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->better_website_performance = new \Better_Website_Performance\Better_Website_Performance();
	}

	public function tearDown(): void {
		remove_filter( 'load_textdomain_mofile', [ $this, '_change_textdomain_mofile' ] );
		remove_filter( 'locale', [ $this, '_change_locale' ] );
		unload_textdomain( 'better-website-performance' );
		parent::tearDown();
	}

	/**
	 * @test
	 * @group basic
	 */
	function public_variable() {
		$this->assertIsArray( $this->better_website_performance->plugin_data );
		$this->assertEmpty( $this->better_website_performance->plugin_data );
	}

	/**
	 * @test
	 * @group basic
	 */
	function basic() {
		$this->assertMatchesRegularExpression( '#/better-website-performance/better-website-performance.php$#', BETTER_WEBSITE_PERFORMANCE );
		$this->assertTrue( class_exists( '\Better_Website_Performance\Better_Website_Performance' ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	function constructor() {
		$this->assertSame( 10, has_filter( 'plugins_loaded', [ $this->better_website_performance, 'load_plugin_data' ] ) );

		$this->assertSame( 10, has_action( 'plugins_loaded', [ $this->better_website_performance, 'init' ] ) );
		$this->assertSame( 10, has_action( 'plugins_loaded', [ $this->better_website_performance, 'load_class_functions' ] ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	function init() {
		$this->better_website_performance->init();

		$this->assertSame( 10, has_action( 'init', [ $this->better_website_performance, 'load_textdomain' ] ) );
		$this->assertSame( 10, has_filter( 'plugin_row_meta', [ $this->better_website_performance, 'plugin_metadata_links' ] ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function load_textdomain() {
		$loaded = $this->better_website_performance->load_textdomain();
		$this->assertFalse( $loaded );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function load_textdomain_mofile() {
		add_filter( 'locale', [ $this, '_change_locale' ] );
		add_filter( 'load_textdomain_mofile', [ $this, '_change_textdomain_mofile' ], 10, 2 );

		$loaded = $this->better_website_performance->load_textdomain();
		$this->assertTrue( $loaded );
	}

	/**
	 * hook for load_textdomain
	 */
	function _change_locale( $locale ) {
		return 'ja';
	}

	function _change_textdomain_mofile( $mofile, $domain ) {
		if ( $domain === 'better-website-performance' ) {
			$locale = determine_locale();
			$mofile = plugin_dir_path( BETTER_WEBSITE_PERFORMANCE ) . 'languages/better-website-performance-' . $locale . '.mo';

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
		$this->better_website_performance->load_class_functions();

		$this->assertTrue( class_exists( '\Better_Website_Performance\Customizer\Panel' ) );
		$this->assertTrue( class_exists( '\Better_Website_Performance\Customizer\Sanitize' ) );

		$this->assertTrue( class_exists( '\Better_Website_Performance\Wp_Custom_Css\Wp_Custom_Css' ) );

		$this->assertTrue( class_exists( '\Better_Website_Performance\Wp_Head\Wp_Head' ) );
		$this->assertTrue( class_exists( '\Better_Website_Performance\Resource_Hints\Resource_Hints' ) );
		$this->assertTrue( class_exists( '\Better_Website_Performance\Preload\Preload' ) );
		$this->assertTrue( class_exists( '\Better_Website_Performance\Emoji\Emoji' ) );
		$this->assertTrue( class_exists( '\Better_Website_Performance\Image_Srcset\Image_Srcset' ) );
		$this->assertTrue( class_exists( '\Better_Website_Performance\Jquery\Jquery' ) );

	}

	/**
	 * @test
	 * @group basic
	 */
	public function plugin_metadata_links() {
		$links = $this->better_website_performance->plugin_metadata_links( [], plugin_basename( BETTER_WEBSITE_PERFORMANCE ) );
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
