<?php
/**
 * Async JavaScript control
 *
 * @package Webby_Performance
 * @since 1.0.0
 */

namespace Webby_Performance\JavaScript;

/**
 * class JavaScript
 *
 * @since 1.0.0
 */
class Async {
	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $section_id
	 */
	public $section_id = 'webby_performance_async_javascript';

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
	public $options_name = 'webby_performance_async_javascript_options';

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
		'async'   => '',
		'exclude' => '',
	];

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var array $exclude_handles
	 */
	public $exclude_handles = [];

	public function __construct() {
		add_action( 'init', [ $this, 'setup_exclude_script' ] );
		add_action( 'customize_register', [ $this, 'customizer' ] );
		add_action( 'script_loader_tag', [ $this, 'async_script_tag' ], 10, 3 );
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
			return apply_filters( 'webby_performance/async_javascript/get_options', $options, $this->type, $default_options );
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
			return apply_filters( 'webby_performance/async_javascript/get_option', $options[ $option_name ], $option_name, $this->type, $default_options );
		}
		else {
			return null;
		}
	}

	public function async_script_tag( $tag, $handle, $src ) {
		if ( is_admin() ) {
			return $tag;
		}

		if ( $this->validate_exclude_script( $handle ) ) {
			return $tag;
		}

		$async = $this->get_options( 'async' );

		if ( $async === 'async' ) {
			$edited_tag = str_replace( 'src=', "async='async' src=", $tag );
			return apply_filters( 'webby_performance/async_javascript/edited_tag', $edited_tag, $tag, $handle, $src );
		}
		else if ( $async === 'defer' ) {
			$edited_tag = str_replace( 'src=', "defer='defer' src=", $tag );
			return apply_filters( 'webby_performance/async_javascript/edited_tag', $edited_tag, $tag, $handle, $src );
		}

		return $tag;
	}

	public function validate_exclude_script( $handle ) {
		return in_array( $handle, $this->exclude_handles, false );
	}

	public function setup_exclude_script() {
		$option = $this->get_options( 'exclude' );
		$handles = preg_split( '/\R/', $option, -1, PREG_SPLIT_NO_EMPTY );
		$this->exclude_handles = apply_filters( 'webby_performance/async_javascript/setup_exclude', $handles );
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
				'title'    => __( 'JavaScript', 'webby-performance' ),
				'priority' => $this->section_priority,
				'panel'    => 'webby_performance_settings',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[async]',
			[
				'default'           => $default_options['async'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Webby_Performance\Customizer\Sanitize', 'sanitize_radio' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[async]',
			[
				'label'   => __( 'Async type', 'webby-performance' ),
				'section' => $this->section_id,
				'type'    => 'radio',
				'choices' => [
					''       => 'None',
					'async'  => 'Async',
					'defer'  => 'Defer',
				],
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[exclude]',
			[
				'default'           => $default_options['exclude'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => '',
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[exclude]',
			[
				'label'       => __( 'Exclude', 'webby-performance' ),
				'section'     => $this->section_id,
				'type'        => 'textarea',
				'description' => __( 'Enter handle names to exclude.', 'webby-performance' ),
			]
		);

	}
}
