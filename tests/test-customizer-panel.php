<?php
/**
 * Class Test_Customizer_Panel
 *
 * @package Better_Website_Performance
 */

class Test_Customizer_Panel extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		$this->panel = new \Better_Website_Performance\Customizer\Panel();

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
	 * @group Customizer_Panel
	 */
	public function public_variable() {
		$this->assertSame( 'edit_theme_options', $this->panel->capability );
	}

	/**
	 * @test
	 * @group Customizer_Panel
	 */
	public function constructor() {
		$this->assertSame( 10, has_filter( 'customize_register', [ $this->panel, 'register_panel' ] ) );
	}

	/**
	 * @test
	 * @group Customizer_Panel
	 */
	public function register_panel() {
		$panel = $this->wp_customize->get_panel( 'better_website_performance_settings' );
		$this->assertSame( 'better_website_performance_settings', $panel->id );
		$this->assertSame( 300, $panel->priority );
		$this->assertSame( 'edit_theme_options', $panel->capability );
		$this->assertSame( 'Performance Settings (Better Website Performance)', $panel->title );
	}

}
