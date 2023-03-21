<?php
/**
 * Class Test_Customizer_Emoji
 *
 * @package Emoji
 */

class Test_Customizer_Emoji extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->emoji = new \Webby_Performance\Emoji\Emoji();

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

	/**
	 * @test
	 * @group Emoji
	 */
	public function section() {
		$section = $this->wp_customize->get_section( 'webby_performance_emoji' );
		$this->assertSame( 'webby_performance_emoji', $section->id );
		$this->assertSame( 160, $section->priority );
		$this->assertSame( 'webby_performance_settings', $section->panel );
		$this->assertSame( 'edit_theme_options', $section->capability );
		$this->assertSame( 'Emoji resource', $section->title );
	}

	/**
	 * @test
	 * @group Emoji
	 */
	public function setting_and_control() {
		$setting = $this->wp_customize->get_setting( 'webby_performance_emoji_options[emoji]' );
		$this->assertSame( 'webby_performance_emoji_options[emoji]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'manage_options', $setting->capability );
		$this->assertTrue( $setting->default );
		$this->assertTrue( in_array( 'sanitize_checkbox_boolean', $setting->sanitize_callback ) );
		$this->assertSame( 10, has_filter( "customize_sanitize_{$setting->id}", $setting->sanitize_callback ) );

		$this->assertTrue( $setting->value() );

		$control = $this->wp_customize->get_control( 'webby_performance_emoji_options[emoji]' );
		$this->assertSame( 'webby_performance_emoji', $control->section );
		$this->assertSame( 'checkbox', $control->type );
	}

	/**
	 * @test
	 * @group Emoji
	 */
	public function save_case_normal() {
		$this->wp_customize->set_post_value( 'webby_performance_emoji_options[emoji]', true );
		$setting = $this->wp_customize->get_setting( 'webby_performance_emoji_options[emoji]' );
		$setting->save();
		$this->assertTrue( $setting->value() );

		$option = $this->emoji->get_options( 'emoji' );
		$this->assertTrue( $option );

		$this->wp_customize->set_post_value( 'webby_performance_emoji_options[emoji]', false );
		$setting = $this->wp_customize->get_setting( 'webby_performance_emoji_options[emoji]' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->emoji->get_options( 'emoji' );
		$this->assertFalse( $option );

	}

	/**
	 * @test
	 * @group Emoji
	 */
	public function save_case_sanitize_callback() {
		$this->wp_customize->set_post_value( 'webby_performance_emoji_options[emoji]', 'aaa' );
		$setting = $this->wp_customize->get_setting( 'webby_performance_emoji_options[emoji]' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->emoji->get_options( 'emoji' );
		$this->assertFalse( $option );
	}

}
