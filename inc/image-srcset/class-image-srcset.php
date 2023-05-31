<?php
/**
 * Image srcset
 *
 * @package Better_Website_Performance
 * @since 1.0.0
 */

namespace Better_Website_Performance\Image_Srcset;

/**
 * class Image_Srcset
 *
 * @since 1.0.0
 */
class Image_Srcset {
	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $section_id
	 */
	public $section_id = 'better_website_performance_image_srcset';

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var int $section_priority
	 */
	public $section_priority = 160;

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $options_name
	 */
	public $options_name = 'better_website_performance_image_srcset_options';

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
		'image_srcset' => true,
	];

	public function __construct() {
		add_action( 'customize_register', [ $this, 'customizer' ] );
		add_action( 'init', [ $this, 'init' ] );
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
			return apply_filters( 'better_website_performance/image_srcset/get_options', $options, $this->type, $default_options );
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
			return apply_filters( 'better_website_performance/image_srcset/get_option', $options[ $option_name ], $option_name, $this->type, $default_options );
		}
		else {
			return null;
		}
	}

	public function init() {
		$image_srcset = $this->get_options( 'image_srcset' );
		if ( ! $image_srcset ) {
			add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
		}
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

		$wp_customize->add_section(
			$this->section_id,
			[
				'title'    => __( 'Image Srcset', 'better-website-performance' ),
				'priority' => $this->section_priority,
				'panel'    => 'better_website_performance_settings',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[image_srcset]',
			[
				'default'           => $default_options['image_srcset'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[image_srcset]',
			[
				'label'   => __( 'Enable Image Srcset', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);
	}
}
