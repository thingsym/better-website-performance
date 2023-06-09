<?php
/**
 * WP Custom css
 *
 * @package Better_Website_Performance
 * @since 1.0.0
 */

namespace Better_Website_Performance\Wp_Custom_Css;

/**
 * class Wp_Custom_Css
 *
 * @since 1.0.0
 */
class Wp_Custom_Css {

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $section_id
	 */
	public $section_id = 'custom_css';

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $options_name
	 */
	public $options_name = 'better_website_performance_wp_custom_css_options';

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $type   types of settings
	 */
	public $type = 'option';

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $capability
	 */
	public $capability = 'manage_options';

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var array $default_options
	 */
	public $default_options = [
		'footer' => false,
	];

	public function __construct() {
		add_action( 'customize_register', [ $this, 'customizer' ] );
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'customize_controls_print_styles', [ $this, 'customize_control_enqueue_styles' ] );
	}

	/**
	 * Returns the options array or value.
	 *
	 * @access public
	 *
	 * @param string $option_name  The option name or modification name via argument.
	 *
	 * @return mixed|null
	 *
	 * @since 1.0.0
	 */
	public function get_options( $option_name = null ) {
		if ( ! $this->type ) {
			return null;
		}

		$default_options = $this->default_options;
		$options         = null;

		if ( $this->type === 'option' ) {
			$options = get_option( $this->options_name, $default_options );
		}
		elseif ( $this->type === 'theme_mod' ) {
			$options = get_theme_mod( $this->options_name, $default_options );
		}

		$options = array_merge( $default_options, $options );

		if ( is_null( $option_name ) ) {
			/**
			 * Filters the options.
			 *
			 * @param mixed     $options     The option values or modification values.
			 * @param string    $type        The option or theme_mod.
			 * @param mixed     $default     Default value to return if the option does not exist.
			 *
			 * @since 1.0.0
			 */
			return apply_filters( 'better_website_performance/wp_custom_css/get_options', $options, $this->type, $default_options );
		}

		if ( array_key_exists( $option_name, $options ) ) {
			/**
			 * Filters the option.
			 *
			 * @param mixed     $option          The option value or modification value.
			 * @param string    $option_name     The option name or modification name via argument.
			 * @param string    $type            The option or theme_mod.
			 * @param mixed     $default         Default value to return if the option does not exist.
			 *
			 * @since 1.0.0
			 */
			return apply_filters( 'better_website_performance/wp_custom_css/get_option', $options[ $option_name ], $option_name, $this->type, $default_options );
		}
		else {
			return null;
		}
	}

	public function init() {
		$footer = $this->get_options( 'footer' );

		if ( $footer ) {
			remove_action( 'wp_head', 'wp_custom_css_cb', 101 );
			add_action( 'wp_footer', 'wp_custom_css_cb', 101 );
		}
	}

	public function customize_control_enqueue_styles() {
		?>
<style>
.customize-section-description-container + #customize-control-custom_css {
	margin-left: -12px;
}
.customize-control-code_editor .CodeMirror {
	height: calc(100vh - 240px);
}
</style>
		<?php
	}

	/**
	 * Implements theme options into Theme Customizer
	 *
	 * @param object $wp_customize Theme Customizer object
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function customizer( $wp_customize ) {
		if ( ! isset( $wp_customize ) ) {
			return;
		}

		$default_options = $this->default_options;

		$wp_customize->add_setting(
			$this->options_name . '[footer]',
			[
				'default'           => $default_options['footer'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[footer]',
			[
				'label'   => __( 'Place custom CSS in the footer', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);
	}

	/**
	 * Uninstall callback for static class method \Better_Website_Performance\Better_Website_Performance::uninstall()
	 *
	 * @access static
	 *
	 * @return void
	 *
	 * @since 1.1.0
	 */
	public static function uninstall() {
		delete_option( 'better_website_performance_wp_custom_css_options' );
	}

}
