<?php
/**
 * Class Test_Customizer_Jquery
 *
 * @package Jquery
 */

class Test_Customizer_Jquery extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->jquery = new \Webby_Performance\Jquery\Jquery();

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
		delete_option( $this->jquery->options_name );
		parent::tearDown();
	}

	/**
	 * @test
	 * @group Jquery
	 */
	public function section() {
		$section = $this->wp_customize->get_section( 'webby_performance_jquery' );
		$this->assertSame( 'webby_performance_jquery', $section->id );
		$this->assertSame( 160, $section->priority );
		$this->assertSame( 'webby_performance_settings', $section->panel );
		$this->assertSame( 'edit_theme_options', $section->capability );
		$this->assertSame( 'jQuery', $section->title );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	public function setting_and_control() {
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[jquery]' );
		$this->assertSame( 'webby_performance_jquery_options[jquery]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'manage_options', $setting->capability );
		$this->assertTrue( $setting->default );
		$this->assertTrue( in_array( 'sanitize_checkbox_boolean', $setting->sanitize_callback ) );
		$this->assertSame( 10, has_filter( "customize_sanitize_{$setting->id}", $setting->sanitize_callback ) );

		$this->assertTrue( $setting->value() );

		$control = $this->wp_customize->get_control( 'webby_performance_jquery_options[jquery]' );
		$this->assertSame( 'webby_performance_jquery', $control->section );
		$this->assertSame( 'checkbox', $control->type );

		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[jquery_migrate]' );
		$this->assertSame( 'webby_performance_jquery_options[jquery_migrate]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'manage_options', $setting->capability );
		$this->assertTrue( $setting->default );
		$this->assertTrue( in_array( 'sanitize_checkbox_boolean', $setting->sanitize_callback ) );
		$this->assertSame( 10, has_filter( "customize_sanitize_{$setting->id}", $setting->sanitize_callback ) );

		$this->assertTrue( $setting->value() );

		$control = $this->wp_customize->get_control( 'webby_performance_jquery_options[jquery_migrate]' );
		$this->assertSame( 'webby_performance_jquery', $control->section );
		$this->assertSame( 'checkbox', $control->type );

		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[in_footer]' );
		$this->assertSame( 'webby_performance_jquery_options[in_footer]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'manage_options', $setting->capability );
		$this->assertFalse( $setting->default );
		$this->assertTrue( in_array( 'sanitize_checkbox_boolean', $setting->sanitize_callback ) );
		$this->assertSame( 10, has_filter( "customize_sanitize_{$setting->id}", $setting->sanitize_callback ) );

		$this->assertFalse( $setting->value() );

		$control = $this->wp_customize->get_control( 'webby_performance_jquery_options[in_footer]' );
		$this->assertSame( 'webby_performance_jquery', $control->section );
		$this->assertSame( 'checkbox', $control->type );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	public function save_case_normal() {
		$this->wp_customize->set_post_value( 'webby_performance_jquery_options[jquery]', true );
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[jquery]' );
		$setting->save();
		$this->assertTrue( $setting->value() );

		$option = $this->jquery->get_options( 'jquery' );
		$this->assertTrue( $option );

		$this->wp_customize->set_post_value( 'webby_performance_jquery_options[jquery]', false );
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[jquery]' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->jquery->get_options( 'jquery' );
		$this->assertFalse( $option );

		$this->wp_customize->set_post_value( 'webby_performance_jquery_options[jquery_migrate]', true );
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[jquery_migrate]' );
		$setting->save();
		$this->assertTrue( $setting->value() );

		$option = $this->jquery->get_options( 'jquery_migrate' );
		$this->assertTrue( $option );

		$this->wp_customize->set_post_value( 'webby_performance_jquery_options[jquery_migrate]', false );
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[jquery_migrate]' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->jquery->get_options( 'jquery_migrate' );
		$this->assertFalse( $option );

		$this->wp_customize->set_post_value( 'webby_performance_jquery_options[jquery]', true );
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[jquery]' );
		$setting->save();
		$this->assertTrue( $setting->value() );

		$option = $this->jquery->get_options( 'jquery' );
		$this->assertTrue( $option );

		$this->wp_customize->set_post_value( 'webby_performance_jquery_options[jquery]', false );
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[jquery]' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->jquery->get_options( 'jquery' );
		$this->assertFalse( $option );

		$this->wp_customize->set_post_value( 'webby_performance_jquery_options[in_footer]', true );
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[in_footer]' );
		$setting->save();
		$this->assertTrue( $setting->value() );

		$option = $this->jquery->get_options( 'in_footer' );
		$this->assertTrue( $option );

		$this->wp_customize->set_post_value( 'webby_performance_jquery_options[in_footer]', false );
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[in_footer]' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->jquery->get_options( 'in_footer' );
		$this->assertFalse( $option );
	}

	/**
	 * @test
	 * @group Jquery
	 */
	public function save_case_sanitize_callback() {
		$this->wp_customize->set_post_value( 'webby_performance_jquery_options[jquery]', 'aaa' );
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[jquery]' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->jquery->get_options( 'jquery' );
		$this->assertFalse( $option );

		$this->wp_customize->set_post_value( 'webby_performance_jquery_options[jquery_migrate]', 'aaa' );
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[jquery_migrate]' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->jquery->get_options( 'jquery_migrate' );
		$this->assertFalse( $option );

		$this->wp_customize->set_post_value( 'webby_performance_jquery_options[in_footer]', 'aaa' );
		$setting = $this->wp_customize->get_setting( 'webby_performance_jquery_options[in_footer]' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->jquery->get_options( 'in_footer' );
		$this->assertFalse( $option );
	}

}
