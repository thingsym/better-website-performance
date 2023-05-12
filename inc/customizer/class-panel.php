<?php
/**
 * Panel for Customizer
 *
 * @package Webby_Performance
 * @since 1.0.0
 */

namespace Webby_Performance\Customizer;

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
			'webby_performance_settings',
			[
				'title'    => __( 'Performance Settings (Webby Performance)', 'webby-performance' ),
				'priority' => 300,
			]
		);
	}
}
