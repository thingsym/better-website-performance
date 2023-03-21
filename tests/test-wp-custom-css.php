<?php
/**
 * Class Test_Wp_Custom_Css
 *
 * @package Webby_Performance
 */

/**
 * Basic test case.
 */
class Test_Wp_Custom_Css extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->wp_custom_css = new \Webby_Performance\Wp_Custom_Css\Wp_Custom_Css();
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	function public_variable() {
		$this->assertSame( 'custom_css', $this->wp_custom_css->section_id );
		$this->assertSame( 'webby_performance_wp_custom_css_options', $this->wp_custom_css->options_name );
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
	function constructor() {
		$this->assertSame( 10, has_filter( 'customize_register', [ $this->wp_custom_css, 'customizer' ] ) );
		$this->assertSame( 10, has_action( 'init', [ $this->wp_custom_css, 'init' ] ) );
		$this->assertSame( 10, has_action( 'customize_controls_print_styles', [ $this->wp_custom_css, 'customize_control_enqueue_styles' ] ) );
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	function get_options_default() {
		$options = $this->wp_custom_css->get_options();
		$expected = [
			'footer' => false,
		];

		$this->assertSame( $expected, $options );
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	function get_options_case_1() {
		$options = array(
			'footer' => false,
		);

		update_option( 'webby_performance_wp_custom_css_options', $options );

		$actual = $this->wp_custom_css->get_options();

		$this->assertFalse( $actual['footer'] );

		$options = array(
			'footer' => true,
		);

		update_option( 'webby_performance_wp_custom_css_options', $options );

		$actual = $this->wp_custom_css->get_options();

		$this->assertTrue( $actual['footer'] );
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	public function get_options_case_filters() {
		$options = array(
			'footer' => true,
		);

		update_option( 'webby_performance_wp_custom_css_options', $options );

		add_filter( 'webby_performance/wp_custom_css/get_options', array( $this, '_filter_options' ), 10 );

		$actual = $this->wp_custom_css->get_options();
		$this->assertFalse( $actual['footer'] );

		add_filter( 'webby_performance/wp_custom_css/get_option', array( $this, '_filter_option' ), 10, 2 );

		$actual = $this->wp_custom_css->get_options( 'footer' );
		$this->assertFalse( $actual );
	}

	public function _filter_options( $options ) {
		$expected = array(
			'footer' => true,
		);
		$this->assertSame( $expected, $options );

		$options = array(
			'footer' => false,
		);

		return $options;
	}

	public function _filter_option( $option, $name ) {
		$this->assertTrue( $option );
		$this->assertSame( $name, 'footer' );

		$option = false;

		return $option;
	}

	/**
	 * @test
	 * @group Wp_Custom_Css
	 */
	function init() {
		$this->wp_custom_css->init();

		$this->assertFalse( has_filter( 'wp_footer', 'wp_custom_css_cb' ) );
		$this->assertSame( 101, has_filter( 'wp_head', 'wp_custom_css_cb' ) );

		$options = array(
			'footer' => true,
		);

		update_option( 'webby_performance_wp_custom_css_options', $options );

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
