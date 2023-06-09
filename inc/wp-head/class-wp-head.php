<?php
/**
 * HTML Head
 *
 * @package Better_Website_Performance
 * @since 1.0.0
 */

namespace Better_Website_Performance\Wp_Head;

/**
 * class Wp_Head
 *
 * @since 1.0.0
 */
class Wp_Head {
	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $section_id
	 */
	public $section_id = 'better_website_performance_wp_head';

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
	public $options_name = 'better_website_performance_wp_head_options';

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
		'feed_links'                    => true,
		'feed_links_extra'              => true,
		'rsd_link'                      => true,
		'wlwmanifest_link'              => true,
		'wp_generator'                  => true,
		'rel_canonical'                 => true,
		'wp_shortlink_wp_head'          => true,
		'rest_output_link_wp_head'      => true,
		'wp_oembed_add_discovery_links' => true,
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
			return apply_filters( 'better_website_performance/wp_head/get_options', $options, $this->type, $default_options );
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
			return apply_filters( 'better_website_performance/wp_head/get_option', $options[ $option_name ], $option_name, $this->type, $default_options );
		}
		else {
			return null;
		}
	}

	public function init() {
		$wp_head = $this->get_options();

		if ( ! $wp_head['feed_links'] ) {
			remove_action( 'wp_head', 'feed_links', 2 );
		}
		if ( ! $wp_head['feed_links_extra'] ) {
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		}
		if ( ! $wp_head['rsd_link'] ) {
			remove_action( 'wp_head', 'rsd_link' );
		}
		if ( ! $wp_head['wlwmanifest_link'] ) {
			remove_action( 'wp_head', 'wlwmanifest_link' );
		}
		if ( ! $wp_head['wp_generator'] ) {
			remove_action( 'wp_head', 'wp_generator' );
		}
		if ( ! $wp_head['rel_canonical'] ) {
			remove_action( 'wp_head', 'rel_canonical' );
		}
		if ( ! $wp_head['wp_shortlink_wp_head'] ) {
			remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		}
		if ( ! $wp_head['rest_output_link_wp_head'] ) {
			remove_action( 'wp_head', 'rest_output_link_wp_head' );
		}
		if ( ! $wp_head['wp_oembed_add_discovery_links'] ) {
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
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
				'title'    => __( 'HTML Head', 'better-website-performance' ),
				'priority' => $this->section_priority,
				'panel'    => 'better_website_performance_settings',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[feed_links]',
			[
				'default'           => $default_options['feed_links'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[feed_links]',
			[
				'label'   => __( 'Display the links to the general feeds', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[feed_links_extra]',
			[
				'default'           => $default_options['feed_links_extra'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[feed_links_extra]',
			[
				'label'   => __( 'Display the links to the extra feeds (such as category feeds)', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[rsd_link]',
			[
				'default'           => $default_options['rsd_link'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[rsd_link]',
			[
				'label'   => __( 'Display Really Simple Discovery (EditURI)', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[wlwmanifest_link]',
			[
				'default'           => $default_options['wlwmanifest_link'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[wlwmanifest_link]',
			[
				'label'   => __( 'Display Windows Live Writer (wlwmanifest)', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[wp_generator]',
			[
				'default'           => $default_options['wp_generator'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[wp_generator]',
			[
				'label'   => __( 'Display WordPress generator', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[rel_canonical]',
			[
				'default'           => $default_options['rel_canonical'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[rel_canonical]',
			[
				'label'   => __( 'Display canonical', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[wp_shortlink_wp_head]',
			[
				'default'           => $default_options['wp_shortlink_wp_head'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[wp_shortlink_wp_head]',
			[
				'label'   => __( 'Display shortlink', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[rest_output_link_wp_head]',
			[
				'default'           => $default_options['rest_output_link_wp_head'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[rest_output_link_wp_head]',
			[
				'label'   => __( 'Display the REST API link', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[wp_oembed_add_discovery_links]',
			[
				'default'           => $default_options['wp_oembed_add_discovery_links'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[wp_oembed_add_discovery_links]',
			[
				'label'   => __( 'Display oEmbed discovery links', 'better-website-performance' ),
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
		delete_option( 'better_website_performance_wp_head_options' );
	}

}
