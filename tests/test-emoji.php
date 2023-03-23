<?php
/**
 * Class Test_Emoji
 *
 * @package Webby_Performance
 */

/**
 * Basic test case.
 */
class Test_Emoji extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->emoji = new \Webby_Performance\Emoji\Emoji();
	}

	/**
	 * @test
	 * @group Emoji
	 */
	function public_variable() {
		$this->assertSame( 'webby_performance_emoji', $this->emoji->section_id );
		$this->assertSame( 160, $this->emoji->section_priority );
		$this->assertSame( 'webby_performance_emoji_options', $this->emoji->options_name );
		$this->assertSame( 'option', $this->emoji->type );
		$this->assertSame( 'manage_options', $this->emoji->capability );

		$expected = [
			'emoji' => true,
		];
		$this->assertSame( $expected, $this->emoji->default_options );
	}

	/**
	 * @test
	 * @group Emoji
	 */
	function constructor() {
		$this->assertSame( 10, has_filter( 'customize_register', [ $this->emoji, 'customizer' ] ) );
		$this->assertSame( 10, has_action( 'init', [ $this->emoji, 'init' ] ) );
	}

	/**
	 * @test
	 * @group Emoji
	 */
	function get_options_default() {
		$options = $this->emoji->get_options();
		$expected = [
			'emoji' => true,
		];

		$this->assertSame( $expected, $options );
	}

	/**
	 * @test
	 * @group Emoji
	 */
	function get_options_case_1() {
		$options = array(
			'emoji' => false,
		);

		update_option( 'webby_performance_emoji_options', $options );

		$actual = $this->emoji->get_options();

		$this->assertFalse( $actual['emoji'] );

		$options = array(
			'emoji' => true,
		);

		update_option( 'webby_performance_emoji_options', $options );

		$actual = $this->emoji->get_options();

		$this->assertTrue( $actual['emoji'] );
	}

	/**
	 * @test
	 * @group Emoji
	 */
	public function get_options_case_filters() {
		$options = array(
			'emoji' => true,
		);

		update_option( 'webby_performance_emoji_options', $options );

		add_filter( 'webby_performance/emoji/get_options', array( $this, '_filter_options' ), 10 );

		$actual = $this->emoji->get_options();
		$this->assertFalse( $actual['emoji'] );

		add_filter( 'webby_performance/emoji/get_option', array( $this, '_filter_option' ), 10, 2 );

		$actual = $this->emoji->get_options( 'emoji' );
		$this->assertFalse( $actual );
	}

	public function _filter_options( $options ) {
		$expected = array(
			'emoji' => true,
		);
		$this->assertSame( $expected, $options );

		$options = array(
			'emoji' => false,
		);

		return $options;
	}

	public function _filter_option( $option, $name ) {
		$this->assertTrue( $option );
		$this->assertSame( $name, 'emoji' );

		$option = false;

		return $option;
	}

	/**
	 * @test
	 * @group Emoji
	 */
	function init() {
		$this->emoji->init();

		$this->assertSame( 7, has_filter( 'wp_head', 'print_emoji_detection_script' ) );
		$this->assertSame( 10, has_filter( 'admin_print_scripts', 'print_emoji_detection_script' ) );
		$this->assertSame( 10, has_filter( 'wp_print_styles', 'print_emoji_styles' ) );
		$this->assertSame( 10, has_filter( 'admin_print_styles', 'print_emoji_styles' ) );
		$this->assertSame( 10, has_filter( 'the_content_feed', 'wp_staticize_emoji' ) );
		$this->assertSame( 10, has_filter( 'comment_text_rss', 'wp_staticize_emoji' ) );
		$this->assertSame( 10, has_filter( 'wp_mail', 'wp_staticize_emoji_for_email' ) );

		$options = array(
			'emoji' => false,
		);

		update_option( 'webby_performance_emoji_options', $options );

		$this->emoji->init();

		$this->assertFalse( has_filter( 'wp_head', 'print_emoji_detection_script' ) );
		$this->assertFalse( has_filter( 'admin_print_scripts', 'print_emoji_detection_script' ) );
		$this->assertFalse( has_filter( 'wp_print_styles', 'print_emoji_styles' ) );
		$this->assertFalse( has_filter( 'admin_print_styles', 'print_emoji_styles' ) );
		$this->assertFalse( has_filter( 'the_content_feed', 'wp_staticize_emoji' ) );
		$this->assertFalse( has_filter( 'comment_text_rss', 'wp_staticize_emoji' ) );
		$this->assertFalse( has_filter( 'wp_mail', 'wp_staticize_emoji_for_email' ) );
	}

}