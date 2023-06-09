<?php
/**
 * Class Test_Wp_Custom_Css
 *
 * @package Better_Website_Performance
 */

/**
 * Basic test case.
 */
class Test_Wp_Custom_Css extends WP_UnitTestCase {
	public $wp_custom_css;

	public function setUp(): void {
		parent::setUp();
		$this->wp_custom_css = new \Better_Website_Performance\Wp_Custom_Css\Wp_Custom_Css();
	}

	public function tearDown(): void {
		delete_option( $this->wp_custom_css->options_name );
		remove_filter( 'better_website_performance/wp_custom_css/get_option', array( $this, '_filter_option' ) );
		remove_filter( 'better_website_performance/wp_custom_css/get_options', array( $this, '_filter_options' ) );
		parent::tearDown();
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	public function public_variable() {
		$this->assertSame( 'custom_css', $this->wp_custom_css->section_id );
		$this->assertSame( 'better_website_performance_wp_custom_css_options', $this->wp_custom_css->options_name );
		$this->assertSame( 'option', $this->wp_custom_css->type );
		$this->assertSame( 'manage_options', $this->wp_custom_css->capability );

		$expected = [
			'footer' => false,
		];
		$this->assertSame( $expected, $this->wp_custom_css->default_options );
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	public function constructor() {
		$this->assertSame( 10, has_filter( 'customize_register', [ $this->wp_custom_css, 'customizer' ] ) );
		$this->assertSame( 10, has_action( 'init', [ $this->wp_custom_css, 'init' ] ) );
		$this->assertSame( 10, has_action( 'customize_controls_print_styles', [ $this->wp_custom_css, 'customize_control_enqueue_styles' ] ) );
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	public function get_options_default() {
		$actual = $this->wp_custom_css->get_options();
		$expected = [
			'footer' => false,
		];

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	public function get_options_case_1() {
		$options = array(
			'footer' => false,
		);

		update_option( $this->wp_custom_css->options_name, $options );

		$actual = $this->wp_custom_css->get_options();

		$this->assertFalse( $actual['footer'] );
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	public function get_options_case_2() {
		$options = array(
			'footer' => true,
		);

		update_option( $this->wp_custom_css->options_name, $options );

		$actual = $this->wp_custom_css->get_options();

		$this->assertTrue( $actual['footer'] );
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	public function get_options_case_filters() {
		add_filter( 'better_website_performance/wp_custom_css/get_options', array( $this, '_filter_options' ), 10 );

		$actual = $this->wp_custom_css->get_options();
		$this->assertTrue( $actual['footer'] );
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	public function get_option_case_filters() {
		add_filter( 'better_website_performance/wp_custom_css/get_option', array( $this, '_filter_option' ), 10, 2 );

		$actual = $this->wp_custom_css->get_options( 'footer' );
		$this->assertTrue( $actual );
	}

	public function _filter_options( $options ) {
		$expected = array(
			'footer' => false,
		);
		$this->assertSame( $expected, $options );

		$options = array(
			'footer' => true,
		);

		return $options;
	}

	public function _filter_option( $option, $name ) {
		$this->assertFalse( $option );
		$this->assertSame( $name, 'footer' );

		$option = true;

		return $option;
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	public function init_default() {
		$this->wp_custom_css->init();

		$this->assertFalse( has_filter( 'wp_footer', 'wp_custom_css_cb' ) );
		$this->assertSame( 101, has_filter( 'wp_head', 'wp_custom_css_cb' ) );
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	public function init_case_1() {
		$options = array(
			'footer' => true,
		);

		update_option( $this->wp_custom_css->options_name, $options );

		$this->wp_custom_css->init();

		$this->assertSame( 101, has_filter( 'wp_footer', 'wp_custom_css_cb' ) );
		$this->assertFalse( has_filter( 'wp_head', 'wp_custom_css_cb' ) );
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	public function customize_control_enqueue_styles() {
		ob_start();
		$this->wp_custom_css->customize_control_enqueue_styles();
		$actual = ob_get_clean();

		$this->assertMatchesRegularExpression( '#<style>#', $actual );
	}

}
