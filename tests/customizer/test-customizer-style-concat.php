<?php
/**
 * Class Test_Customizer_Style_Concat
 *
 * @package Style_Concat
 */

class Test_Customizer_Style_Concat extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->concat_style = new \Webby_Performance\Style\Concat();

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
		delete_option( $this->concat_style->options_name );
		parent::tearDown();
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function section() {
		$section = $this->wp_customize->get_section( 'webby_performance_concat_style' );
		$this->assertSame( 'webby_performance_concat_style', $section->id );
		$this->assertSame( 160, $section->priority );
		$this->assertSame( 'webby_performance_settings', $section->panel );
		$this->assertSame( 'edit_theme_options', $section->capability );
		$this->assertSame( 'Style Sheet', $section->title );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function setting_and_control() {
		$setting = $this->wp_customize->get_setting( 'webby_performance_concat_style_options[loading]' );
		$this->assertSame( 'webby_performance_concat_style_options[loading]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'manage_options', $setting->capability );
		$this->assertSame( 'default', $setting->default );
		$this->assertTrue( in_array( 'sanitize_radio', $setting->sanitize_callback ) );
		$this->assertSame( 10, has_filter( "customize_sanitize_{$setting->id}", $setting->sanitize_callback ) );

		$this->assertSame( 'default', $setting->value() );

		$control = $this->wp_customize->get_control( 'webby_performance_concat_style_options[loading]' );
		$this->assertSame( 'webby_performance_concat_style', $control->section );
		$this->assertSame( 'radio', $control->type );

		$setting = $this->wp_customize->get_setting( 'webby_performance_concat_style_options[minify]' );
		$this->assertSame( 'webby_performance_concat_style_options[minify]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'manage_options', $setting->capability );
		$this->assertFalse( $setting->default );
		$this->assertTrue( in_array( 'sanitize_checkbox_boolean', $setting->sanitize_callback ) );
		$this->assertSame( 10, has_filter( "customize_sanitize_{$setting->id}", $setting->sanitize_callback ) );

		$this->assertFalse( $setting->value() );

		$control = $this->wp_customize->get_control( 'webby_performance_concat_style_options[minify]' );
		$this->assertSame( 'webby_performance_concat_style', $control->section );
		$this->assertSame( 'checkbox', $control->type );

		$setting = $this->wp_customize->get_setting( 'webby_performance_concat_style_options[exclude]' );
		$this->assertSame( 'webby_performance_concat_style_options[exclude]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'manage_options', $setting->capability );
		$this->assertEmpty( $setting->default );
		$this->assertEmpty( $setting->sanitize_callback );
		$this->assertFalse( has_filter( "customize_sanitize_{$setting->id}" ) );

		$this->assertEmpty( $setting->value() );

		$control = $this->wp_customize->get_control( 'webby_performance_concat_style_options[exclude]' );
		$this->assertSame( 'webby_performance_concat_style', $control->section );
		$this->assertSame( 'textarea', $control->type );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function save_case_normal() {
		$this->wp_customize->set_post_value( 'webby_performance_concat_style_options[loading]', 'default' );
		$setting = $this->wp_customize->get_setting( 'webby_performance_concat_style_options[loading]' );
		$setting->save();
		$this->assertSame( 'default', $setting->value() );

		$option = $this->concat_style->get_options( 'loading' );
		$this->assertSame( 'default', $option );

		$this->wp_customize->set_post_value( 'webby_performance_concat_style_options[loading]', 'inline' );
		$setting = $this->wp_customize->get_setting( 'webby_performance_concat_style_options[loading]' );
		$setting->save();
		$this->assertSame( 'inline', $setting->value() );

		$option = $this->concat_style->get_options( 'loading' );
		$this->assertSame( 'inline', $option );

		$this->wp_customize->set_post_value( 'webby_performance_concat_style_options[loading]', 'concat' );
		$setting = $this->wp_customize->get_setting( 'webby_performance_concat_style_options[loading]' );
		$setting->save();
		$this->assertSame( 'concat', $setting->value() );

		$option = $this->concat_style->get_options( 'loading' );
		$this->assertSame( 'concat', $option );

		$this->wp_customize->set_post_value( 'webby_performance_concat_style_options[minify]', true );
		$setting = $this->wp_customize->get_setting( 'webby_performance_concat_style_options[minify]' );
		$setting->save();
		$this->assertTrue( $setting->value() );

		$option = $this->concat_style->get_options( 'minify' );
		$this->assertTrue( $option );

		$this->wp_customize->set_post_value( 'webby_performance_concat_style_options[minify]', false );
		$setting = $this->wp_customize->get_setting( 'webby_performance_concat_style_options[minify]' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->concat_style->get_options( 'minify' );
		$this->assertFalse( $option );
	}

	public function excludeValuesProvider(): array {
		return [
			[ '' ],
			[ 'test-style' ],
			[ 'test1-style
test2-style
test3-style'
			]
		];
	}

	/**
	 * @test
	 * @group Style_Concat
	 * @dataProvider excludeValuesProvider
	 */
	public function save_case_exclude( $value ) {
		$this->wp_customize->set_post_value( 'webby_performance_concat_style_options[exclude]', $value );
		$setting = $this->wp_customize->get_setting( 'webby_performance_concat_style_options[exclude]' );
		$setting->save();
		$this->assertSame( $setting->value(), $value );

		$option = $this->concat_style->get_options( 'exclude' );
		$this->assertSame( $option, $value );
	}

	/**
	 * @test
	 * @group Style_Concat
	 */
	public function save_case_sanitize_callback() {
		$this->wp_customize->set_post_value( 'webby_performance_concat_style_options[loading]', 'aaa' );
		$setting = $this->wp_customize->get_setting( 'webby_performance_concat_style_options[loading]' );
		$setting->save();
		$this->assertSame( 'default', $setting->value() );

		$option = $this->concat_style->get_options( 'loading' );
		$this->assertSame( 'default', $option );

		$this->wp_customize->set_post_value( 'webby_performance_concat_style_options[minify]', 'aaa' );
		$setting = $this->wp_customize->get_setting( 'webby_performance_concat_style_options[minify]' );
		$setting->save();
		$this->assertFalse( $setting->value() );

		$option = $this->concat_style->get_options( 'minify' );
		$this->assertFalse( $option );
	}

}
