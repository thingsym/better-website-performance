<?php
/**
 * Panel for Customizer
 *
 * @package Better_Website_Performance
 * @since 1.0.0
 */

namespace Better_Website_Performance\Customizer;

/**
 * Class Panel
 *
 * @since 1.0.0
 */
class Panel {

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $capability
	 */
	public $capability = 'edit_theme_options';

	public function __construct() {
		add_action( 'customize_register', [ $this, 'register_panel' ] );
	}

	public function register_panel( $wp_customize ) {
		if ( ! isset( $wp_customize ) ) {
			return;
		}

		$wp_customize->add_panel(
			'better_website_performance_settings',
			[
				'title'    => __( 'Performance Settings (Better Website Performance)', 'better-website-performance' ),
				'priority' => 300,
			]
		);
	}
}
