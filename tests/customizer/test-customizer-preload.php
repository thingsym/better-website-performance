<?php
/**
 * Class Test_Customizer_Preload
 *
 * @package Preload
 */

class Test_Customizer_Preload extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->preload = new \Webby_Performance\Preload\Preload();

		require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';

		$user_id = self::factory()->user->create( [
			'role' => 'administrator',
		] );

		if ( is_multisite() ) {
			grant_super_admin( $user_id );
		}

		wp_set_current_user( $user_id );

		global $wp_customize;
		$this->wp_customize = new WP_Customize_Manager();
		$wp_customize       = $this->wp_customize;

		do_action( 'customize_register', $this->wp_customize );
	}

	public function tearDown(): void {
		delete_option( $this->preload->options_name );
	}

	/**
	 * @test
	 * @group Preload
	 */
	public function section() {
		$section = $this->wp_customize->get_section( 'webby_performance_preload' );
		$this->assertSame( 'webby_performance_preload', $section->id );
		$this->assertSame( 180, $section->priority );
		$this->assertSame( 'webby_performance_settings', $section->panel );
		$this->assertSame( 'edit_theme_options', $section->capability );
		$this->assertSame( 'Preload', $section->title );
	}

	/**
	 * @test
	 * @group Preload
	 */
	public function setting_and_control() {
		$setting = $this->wp_customize->get_setting( 'webby_performance_preload_options[preload]' );
		$this->assertSame( 'webby_performance_preload_options[preload]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'manage_options', $setting->capability );
		$this->assertEmpty( $setting->default );
		$this->assertEmpty( $setting->sanitize_callback );
		$this->assertFalse( has_filter( "customize_sanitize_{$setting->id}" ) );

		$this->assertEmpty( $setting->value() );

		$control = $this->wp_customize->get_control( 'webby_performance_preload_options[preload]' );
		$this->assertSame( 'webby_performance_preload', $control->section );
		$this->assertSame( 'textarea', $control->type );
	}

	public function preloadValuesProvider(): array {
		return [
			[ '' ],
			[ '//fonts.googleapis.com/css?family=Montserrat, font, , text/html' ],
			[ '//fonts.googleapis.com/css?family=Montserrat, font, crossorigin , text/html' ],
			[ '//fonts.googleapis.com/css?family=Montserrat, font' ],
			[ '//fonts.googleapis.com/css?family=Montserrat' ],
			[ '//fonts.googleapis.com/css?family=Montserrat, font, , text/html
//fonts.googleapis.com/css?family=Montserrat, font, crossorigin , text/html
//fonts.googleapis.com/css?family=Montserrat, font
//fonts.googleapis.com/css?family=Montserrat'
			]
		];
	}

	/**
	 * @test
	 * @group Preload
	 * @dataProvider preloadValuesProvider
	 */
	public function save_case_normal( $value ) {
		$this->wp_customize->set_post_value( 'webby_performance_preload_options[preload]', $value );
		$setting = $this->wp_customize->get_setting( 'webby_performance_preload_options[preload]' );
		$setting->save();
		$this->assertSame( $setting->value(), $value );

		$option = $this->preload->get_options( 'preload' );
		$this->assertSame( $option, $value );
	}

}
