<?php
/**
 * Class Test_Customizer_Javascript_Async
 *
 * @package Javascript_Async
 */

class Test_Customizer_Javascript_Async extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->javascript_async = new \Better_Website_Performance\JavaScript\Async();

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
		delete_option( $this->javascript_async->options_name );
		parent::tearDown();
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	public function section() {
		$section = $this->wp_customize->get_section( 'better_website_performance_async_javascript' );
		$this->assertSame( 'better_website_performance_async_javascript', $section->id );
		$this->assertSame( 160, $section->priority );
		$this->assertSame( 'better_website_performance_settings', $section->panel );
		$this->assertSame( 'edit_theme_options', $section->capability );
		$this->assertSame( 'JavaScript', $section->title );
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	public function setting_and_control() {
		$setting = $this->wp_customize->get_setting( 'better_website_performance_async_javascript_options[async]' );
		$this->assertSame( 'better_website_performance_async_javascript_options[async]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'manage_options', $setting->capability );
		$this->assertEmpty( $setting->default );
		$this->assertTrue( in_array( 'sanitize_radio', $setting->sanitize_callback ) );
		$this->assertSame( 10, has_filter( "customize_sanitize_{$setting->id}", $setting->sanitize_callback ) );

		$this->assertEmpty( $setting->value() );

		$control = $this->wp_customize->get_control( 'better_website_performance_async_javascript_options[async]' );
		$this->assertSame( 'better_website_performance_async_javascript', $control->section );
		$this->assertSame( 'radio', $control->type );

		$setting = $this->wp_customize->get_setting( 'better_website_performance_async_javascript_options[exclude]' );
		$this->assertSame( 'better_website_performance_async_javascript_options[exclude]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'manage_options', $setting->capability );
		$this->assertEmpty( $setting->default );
		$this->assertEmpty( $setting->sanitize_callback );
		$this->assertFalse( has_filter( "customize_sanitize_{$setting->id}" ) );

		$this->assertEmpty( $setting->value() );

		$control = $this->wp_customize->get_control( 'better_website_performance_async_javascript_options[exclude]' );
		$this->assertSame( 'better_website_performance_async_javascript', $control->section );
		$this->assertSame( 'textarea', $control->type );
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	public function save_case_normal() {
		$this->wp_customize->set_post_value( 'better_website_performance_async_javascript_options[async]', '' );
		$setting = $this->wp_customize->get_setting( 'better_website_performance_async_javascript_options[async]' );
		$setting->save();
		$this->assertEmpty( $setting->value() );

		$option = $this->javascript_async->get_options( 'async' );
		$this->assertEmpty( $option );

		$this->wp_customize->set_post_value( 'better_website_performance_async_javascript_options[async]', 'async' );
		$setting = $this->wp_customize->get_setting( 'better_website_performance_async_javascript_options[async]' );
		$setting->save();
		$this->assertSame( 'async', $setting->value() );

		$option = $this->javascript_async->get_options( 'async' );
		$this->assertSame( 'async', $option );
	}

	public function excludeValuesProvider(): array {
		return [
			[ '' ],
			[ 'test-js' ],
			[ 'test1-js
test2-js
test3-js'
			]
		];
	}

	/**
	 * @test
	 * @group Javascript_Async
	 * @dataProvider excludeValuesProvider
	 */
	public function save_case_exclude( $value ) {
		$this->wp_customize->set_post_value( 'better_website_performance_async_javascript_options[exclude]', $value );
		$setting = $this->wp_customize->get_setting( 'better_website_performance_async_javascript_options[exclude]' );
		$setting->save();
		$this->assertSame( $setting->value(), $value );

		$option = $this->javascript_async->get_options( 'exclude' );
		$this->assertSame( $option, $value );
	}

	/**
	 * @test
	 * @group Javascript_Async
	 */
	public function save_case_sanitize_callback() {
		$this->wp_customize->set_post_value( 'better_website_performance_async_javascript_options[async]', 'aaa' );
		$setting = $this->wp_customize->get_setting( 'better_website_performance_async_javascript_options[async]' );
		$setting->save();
		$this->assertEmpty( $setting->value() );

		$option = $this->javascript_async->get_options( 'async' );
		$this->assertEmpty( $option );
	}

}
