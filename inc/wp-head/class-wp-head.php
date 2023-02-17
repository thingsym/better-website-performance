<?php
/**
 * Wp head
 *
 * @package Webby_Performance
 * @since 1.0.0
 */

namespace Webby_Performance\Wp_Head;

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
	public $section_id = 'webby_performance_wp_head';

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
	public $options_name = 'webby_performance_wp_head_options';

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
		'feed_links'                      => true,
		'feed_links_extra'                => true,
		'rsd_link'                        => true,
		'wlwmanifest_link'                => true,
		'wp_generator'                    => true,
		'wp_shortlink_wp_head'            => true,
		'index_rel_link'                  => true,
		'adjacent_posts_rel_link_wp_head' => true,
		'rest_output_link_wp_head'        => true,
		'wp_oembed_add_discovery_links'   => true,
	];

	public function __construct() {
		add_action( 'customize_register', [ $this, 'customizer' ] );
		add_action( 'init', [ $this, 'setup' ] );
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
			return apply_filters( 'webby_performance/wp_head/get_options', $options, $this->type, $default_options );
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
			return apply_filters( 'webby_performance/wp_head/get_option', $options[ $option_name ], $option_name, $this->type, $default_options );
		}
		else {
			return null;
		}
	}

	public function setup() {
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
		if ( ! $wp_head['index_rel_link'] ) {
			remove_action( 'wp_head', 'index_rel_link' );
		}
		if ( ! $wp_head['wp_shortlink_wp_head'] ) {
			remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		}
		if ( ! $wp_head['adjacent_posts_rel_link_wp_head'] ) {
			remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
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
				'title'    => __( 'WP Head', 'webby-performance' ),
				'priority' => $this->section_priority,
				'panel'    => 'webby_performance_settings',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[feed_links]',
			[
				'default'           => $default_options['feed_links'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Webby_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[feed_links]',
			[
				'label'   => __( 'Enable RSS', 'webby-performance' ),
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
				'sanitize_callback' => [ 'Webby_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[feed_links_extra]',
			[
				'label'   => __( 'Enable extra RSS', 'webby-performance' ),
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
				'sanitize_callback' => [ 'Webby_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[rsd_link]',
			[
				'label'   => __( 'Enable Really Simple Discovery', 'webby-performance' ),
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
				'sanitize_callback' => [ 'Webby_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[wlwmanifest_link]',
			[
				'label'   => __( 'Enable Windows Live Writer', 'webby-performance' ),
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
				'sanitize_callback' => [ 'Webby_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[wp_generator]',
			[
				'label'   => __( 'Enable WordPress generator', 'webby-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[index_rel_link]',
			[
				'default'           => $default_options['index_rel_link'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Webby_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[index_rel_link]',
			[
				'label'   => __( 'Enable index rel link', 'webby-performance' ),
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
				'sanitize_callback' => [ 'Webby_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[wp_shortlink_wp_head]',
			[
				'label'   => __( 'Enable shortlink', 'webby-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[adjacent_posts_rel_link_wp_head]',
			[
				'default'           => $default_options['adjacent_posts_rel_link_wp_head'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Webby_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[adjacent_posts_rel_link_wp_head]',
			[
				'label'   => __( 'Enable prev/next posts link', 'webby-performance' ),
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
				'sanitize_callback' => [ 'Webby_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[rest_output_link_wp_head]',
			[
				'label'   => __( 'Enable the REST API link', 'webby-performance' ),
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
				'sanitize_callback' => [ 'Webby_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[wp_oembed_add_discovery_links]',
			[
				'label'   => __( 'Enable oEmbed discovery links', 'webby-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

	}
}
