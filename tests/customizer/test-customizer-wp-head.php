<?php
/**
 * Class Test_Customizer_Wp_Head
 *
 * @package Wp_Head
 */

class Test_Customizer_Wp_Head extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->wp_head = new \Webby_Performance\Wp_Head\Wp_Head();

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
		delete_option( $this->wp_head->options_name );
		parent::tearDown();
	}

	/**
	 * @test
	 * @group Wp_Head
	 */
	public function section() {
		$section = $this->wp_customize->get_section( 'webby_performance_wp_head' );
		$this->assertSame( 'webby_performance_wp_head', $section->id );
		$this->assertSame( 160, $section->priority );
		$this->assertSame( 'webby_performance_settings', $section->panel );
		$this->assertSame( 'edit_theme_options', $section->capability );
		$this->assertSame( 'WP Head', $section->title );
	}

	public function optionsProvider(): array {
		return [
			[ 'feed_links' ],
			[ 'feed_links_extra' ],
			[ 'rsd_link' ],
			[ 'wlwmanifest_link' ],
			[ 'wp_generator' ],
			[ 'rel_canonical' ],
			[ 'wp_shortlink_wp_head' ],
			[ 'rest_output_link_wp_head' ],
			[ 'wp_oembed_add_discovery_links' ],
		];
	}

	/**
	 * @test
	 * @group Wp_Head
	 * @dataProvider optionsProvider
	 */
	public function setting_and_control( $option_name ) {
		$setting = $this->wp_customize->get_setting( 'webby_performance_wp_head_options[' . $option_name . ']' );
		$this->assertSame( 'webby_performance_wp_head_options[' . $option_name . ']', $setting->id, $option_name );
		$this->assertSame( 'option', $setting->type, $option_name );
		$this->assertSame( 'manage_options', $setting->capability, $option_name );
		$this->assertTrue( $setting->default, $option_name );
		$this->assertTrue( in_array( 'sanitize_checkbox_boolean', $setting->sanitize_callback ), $option_name );
		$this->assertSame( 10, has_filter( "customize_sanitize_{$setting->id}", $setting->sanitize_callback ), $option_name );

		$this->assertTrue( $setting->value(), $option_name );

		$control = $this->wp_customize->get_control( 'webby_performance_wp_head_options[' . $option_name . ']', $option_name );
		$this->assertSame( 'webby_performance_wp_head', $control->section, $option_name );
		$this->assertSame( 'checkbox', $control->type, $option_name );
	}

	/**
	 * @test
	 * @group Wp_Head
	 * @dataProvider optionsProvider
	 */
	public function save_case_normal( $option_name ) {
		$this->wp_customize->set_post_value( 'webby_performance_wp_head_options[' . $option_name . ']', true );
		$setting = $this->wp_customize->get_setting( 'webby_performance_wp_head_options[' . $option_name . ']' );
		$setting->save();
		$this->assertTrue( $setting->value() );

		$option = $this->wp_head->get_options( $option_name );
		$this->assertTrue( $option );

		$this->wp_customize->set_post_value( 'webby_performance_wp_head_options[' . $option_name . ']', false );
		$setting = $this->wp_customize->get_setting( 'webby_performance_wp_head_options[' . $option_name . ']' );
		$setting->save();
		$this->assertFalse( $setting->value() );
	}

	/**
	 * @test
	 * @group Wp_Head
	 * @dataProvider optionsProvider
	 */
	public function save_case_sanitize_callback( $option_name ) {
		$this->wp_customize->set_post_value( 'webby_performance_wp_head_options[' . $option_name . ']', 'aaa' );
		$setting = $this->wp_customize->get_setting( 'webby_performance_wp_head_options[' . $option_name . ']' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->wp_head->get_options( $option_name );
		$this->assertFalse( $option );
	}

}
