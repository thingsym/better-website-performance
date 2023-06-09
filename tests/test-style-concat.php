<?php
/**
 * Class Test_Style_Concat
 *
 * @package Better_Website_Performance
 */

/**
 * Basic test case.
 */
class Test_Style_Concat extends WP_UnitTestCase {
	public $style_concat;

	public function setUp(): void {
		parent::setUp();
		$this->style_concat = new \Better_Website_Performance\Style\Concat();
	}

	public function tearDown(): void {
		delete_option( $this->style_concat->options_name );
		remove_filter( 'better_website_performance/concat_style/get_option', array( $this, '_filter_option' ) );
		remove_filter( 'better_website_performance/concat_style/get_options', array( $this, '_filter_options' ) );

		global $current_screen;
		$current_screen = null;

		global $wp_actions;
		$wp_actions[ 'login_head' ] = null;

	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function public_variable() {
		$this->assertSame( 'better_website_performance_concat_style', $this->style_concat->section_id );
		$this->assertSame( 160, $this->style_concat->section_priority );
		$this->assertSame( 'better_website_performance_concat_style_options', $this->style_concat->options_name );
		$this->assertSame( 'option', $this->style_concat->type );
		$this->assertSame( 'manage_options', $this->style_concat->capability );

		$expected = [
			'loading' => 'default',
			'minify'  => false,
			'exclude' => '',
		];
		$this->assertSame( $expected, $this->style_concat->default_options );

		$this->assertSame( '', $this->style_concat->concat_css );

		$this->assertSame( [], $this->style_concat->exclude_handles );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function constructor() {
		$this->assertSame( 10, has_action( 'init', [ $this->style_concat, 'setup_exclude_style' ] ) );
		$this->assertSame( 10, has_filter( 'customize_register', [ $this->style_concat, 'customizer' ] ) );
		$this->assertSame( 10, has_action( 'style_loader_tag', [ $this->style_concat, 'concat_style_tag' ] ) );
		$this->assertSame( 10, has_action( 'wp_footer', [ $this->style_concat, 'print_concat_style' ] ) );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function get_options_default() {
		$expected = [
			'loading' => 'default',
			'minify'  => false,
			'exclude' => '',
		];

		$actual = $this->style_concat->get_options();

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function get_options_case_1() {
		$expected = [
			'loading' => 'inline',
			'minify'  => true,
			'exclude' => '',
		];

		$options = array(
			'loading' => 'inline',
			'minify'  => true,
			'exclude' => '',
		);

		update_option( $this->style_concat->options_name, $options );

		$actual = $this->style_concat->get_options();

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function get_options_case_filters() {
		$expected = array(
			'loading' => 'concat',
			'minify'  => false,
			'exclude' => '',
		);

		add_filter( 'better_website_performance/concat_style/get_options', array( $this, '_filter_options' ), 10 );

		$actual = $this->style_concat->get_options();
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function get_option_case_filters() {
		add_filter( 'better_website_performance/concat_style/get_option', array( $this, '_filter_option' ), 10, 2 );

		$actual = $this->style_concat->get_options( 'loading' );
		$this->assertSame( 'inline', $actual );
	}

	public function _filter_options( $options ) {
		$expected = array(
			'loading' => 'default',
			'minify'  => false,
			'exclude' => '',
		);
		$this->assertSame( $expected, $options );

		$options = array(
			'loading' => 'concat',
			'minify'  => false,
			'exclude' => '',
		);

		return $options;
	}

	public function _filter_option( $option, $name ) {
		$this->assertSame( 'default', $option );
		$this->assertSame( 'loading', $name );

		$option = 'inline';

		return $option;
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function concat_style_tag() {
		$tag = "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />";

		$actual = $this->style_concat->concat_style_tag( $tag, 'test-css', './tests/test.css' );
		$this->assertSame( "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />", $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function concat_style_tag_admin() {
		set_current_screen( 'dashboard' );

		$tag = "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />";

		$options = array(
			'loading' => 'inline',
			'minify'  => false,
			'exclude' => '',
		);

		update_option( $this->style_concat->options_name, $options );

		$actual = $this->style_concat->concat_style_tag( $tag, 'test-css', './tests/test.css' );
		$this->assertTrue( is_admin() );
		$this->assertSame( "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />", $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function concat_style_tag_on_login_page() {
		// $this->markTestIncomplete( 'This test has not been implemented yet.' );
		global $wp_actions;
		$wp_actions[ 'login_head' ] = 1;
		// do_action( 'login_head' );

		$tag = "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />";

		$options = array(
			'loading' => 'inline',
			'minify'  => false,
			'exclude' => '',
		);

		update_option( $this->style_concat->options_name, $options );

		$actual = $this->style_concat->concat_style_tag( $tag, 'test-css', './tests/test.css' );
		$this->assertSame( 1, did_action( 'login_head' ) );
		$this->assertSame( "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />", $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function concat_style_tag_exclude() {
		$tag = "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />";

		$options = array(
			'loading' => 'concat',
			'minify'  => false,
			'exclude' => 'test-css',
		);

		update_option( $this->style_concat->options_name, $options );

		$this->style_concat->setup_exclude_style();
		$actual = $this->style_concat->concat_style_tag( $tag, 'test-css', './tests/test.css' );
		$this->assertSame( "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />", $actual );

		$options = array(
			'loading' => 'inline',
			'minify'  => false,
			'exclude' => 'test-css',
		);

		update_option( $this->style_concat->options_name, $options );

		$this->style_concat->setup_exclude_style();
		$actual = $this->style_concat->concat_style_tag( $tag, 'test-css', './tests/test.css' );
		$this->assertSame( "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />", $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function concat_style_tag_inline_plus_minify() {
		$tag = "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />";

		$options = array(
			'loading' => 'inline',
			'minify'  => false,
			'exclude' => '',
		);

		update_option( $this->style_concat->options_name, $options );

		$actual = $this->style_concat->concat_style_tag( $tag, 'test-css', './tests/test.css' );

		$this->assertSame( "<style id='better-website-performance-test-css-inline-css'>
body {
	font-size: 100%;
}

</style>
", $actual );

		$options = array(
			'loading' => 'inline',
			'minify'  => true,
			'exclude' => '',
		);
		update_option( $this->style_concat->options_name, $options );

		$actual = $this->style_concat->concat_style_tag( $tag, 'test-css', './tests/test.css' );

		$this->assertSame( "<style id='better-website-performance-test-css-inline-css'>
body{font-size:100%;}
</style>
", $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function concat_style_tag_concat() {
		$this->style_concat->concat_css = 'span {
	font-size: 80%;
}
';

		$options = array(
			'loading' => 'concat',
			'minify'  => false,
			'exclude' => '',
		);
		update_option( $this->style_concat->options_name, $options );

		$tag = "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />";

		$actual = $this->style_concat->concat_style_tag( $tag, 'test-css', './tests/test.css' );

		$this->assertEmpty( $actual );
		$this->assertSame( "span {
	font-size: 80%;
}
body {
	font-size: 100%;
}
", $this->style_concat->concat_css );
	}


	/**
	 * @test
	 * @group Style_Concat
	 */
	public function concat_style_tag_concat_and_minify() {
		$this->style_concat->concat_css = 'span{font-size:80%;}';

		$options = array(
			'loading' => 'concat',
			'minify'  => true,
			'exclude' => '',
		);
		update_option( $this->style_concat->options_name, $options );

		$tag = "<link rel='stylesheet' id='test-css' href='https://example.org/wp-includes/css/test.css' media='all' />";

		$actual = $this->style_concat->concat_style_tag( $tag, 'test-css', './tests/test.css' );

		$this->assertEmpty( $actual );
		$this->assertSame( "span{font-size:80%;}body{font-size:100%;}", $this->style_concat->concat_css );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function print_concat_style() {
		$this->style_concat->concat_css = "body{font-size:100%;}";

		ob_start();
		$this->style_concat->print_concat_style();
		$actual = ob_get_clean();

		$this->assertSame( "<style id='better-website-performance-concat-styles'>
body{font-size:100%;}
</style>
", $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function print_concat_style_admin() {
		set_current_screen( 'dashboard' );

		$this->style_concat->concat_css = "body{font-size:100%;}";

		ob_start();
		$this->style_concat->print_concat_style();
		$actual = ob_get_clean();

		$this->assertTrue( is_admin() );
		$this->assertEmpty( $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function print_concat_style_on_login_page() {
		global $wp_actions;
		$wp_actions[ 'login_head' ] = 1;
		// do_action( 'login_head' );

		$this->style_concat->concat_css = "body{font-size:100%;}";

		ob_start();
		$this->style_concat->print_concat_style();
		$actual = ob_get_clean();

		$this->assertSame( 1, did_action( 'login_head' ) );
		$this->assertEmpty( $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function minify() {
		$css = "		body { font-size: 100% ;  background : #ccccccc;
	margin: 10px;
}";

		$actual = $this->style_concat->minify( $css );

		$this->assertSame( 'body{font-size:100%;background:#ccccccc;margin:10px;}', $actual );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function validate_exclude_style() {
		$this->style_concat->exclude_handles[] = 'test-style';
		$this->assertTrue( $this->style_concat->validate_exclude_style( 'test-style' ) );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function setup_exclude_style() {
		$options = array(
			'loading' => 'default',
			'minify'  => false,
			'exclude' => 'test1-style
test2-style
test3-style',
		);
		update_option( $this->style_concat->options_name, $options );

		$this->style_concat->setup_exclude_style();

		$this->assertTrue( in_array( 'test1-style', $this->style_concat->exclude_handles ) );
		$this->assertTrue( in_array( 'test2-style', $this->style_concat->exclude_handles ) );
		$this->assertTrue( in_array( 'test3-style', $this->style_concat->exclude_handles ) );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function uninstall() {
		update_option( $this->style_concat->options_name, 'uninstall' );

		\Better_Website_Performance\Style\Concat::uninstall();

		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $this->style_concat->options_name ) );

		$this->assertNull( $row );
	}

}
