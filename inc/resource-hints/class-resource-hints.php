<?php
/**
 * Resource Hints
 *
 * @package Webby_Performance
 * @since 1.0.0
 */

namespace Webby_Performance\Resource_Hints;

/**
 * class Resource_Hints
 *
 * @since 1.0.0
 */
class Resource_Hints {
	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $section_id
	 */
	public $section_id = 'webby_performance_resource_hints';

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var int $section_priority
	 */
	public $section_priority = 180;

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $options_name
	 */
	public $options_name = 'webby_performance_resource_hints_options';

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
	public $capability   = 'manage_options';

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var array $default_options
	 */
	public $default_options = [
		'dns_prefetch' => '',
		'preconnect'   => '',
		'prefetch'     => '',
		'prerender'    => '',
	];

	public function __construct() {
		add_action( 'customize_register', [ $this, 'customizer' ] );
		add_action( 'wp_head', [ $this, 'print_tag' ], 2 );
		add_filter( 'wp_resource_hints', [ $this, 'add_resource_hints' ], 10, 2 );
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
			return apply_filters( 'webby_performance/resource_hints/get_options', $options, $this->type, $default_options );
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
			return apply_filters( 'webby_performance/resource_hints/get_option', $options[ $option_name ], $option_name, $this->type, $default_options );
		}
		else {
			return null;
		}
	}

	public function add_resource_hints( $hints, $relation_type ) {
		if ( is_admin() ) {
			return $hints;
		}

		if ( 'dns-prefetch' === $relation_type ) {
			$option = $this->get_options( 'dns_prefetch' );
		}
		elseif ( 'preconnect' === $relation_type ) {
			$option = $this->get_options( 'preconnect' );
		}
		elseif ( 'prefetch' === $relation_type ) {
			$option = $this->get_options( 'prefetch' );
		}
		elseif ( 'prerender' === $relation_type ) {
			$option = $this->get_options( 'prerender' );
		}

		$urls = preg_split( '/\R/', $option, -1, PREG_SPLIT_NO_EMPTY );

		if ( empty( $urls ) ) {
			return $hints;
		}

		foreach ( $urls as $line ) {
			$param = explode( ',', $line );

			$param = array_map( function( $el ) {
				return trim( $el );
			}, $param );

			if ( ! empty( $param[0] ) ) {
				$resources['href'] = $param[0];
			}
			if ( ! empty( $param[1] ) ) {
				$resources['as'] = $param[1];
			}
			if ( ! empty( $param[2] ) ) {
				$resources['crossorigin'] = $param[2];
			}

			$hints[] = $resources;
		}

		return $hints;
	}

	public function print_tag() {
		$meta = '<meta http-equiv="x-dns-prefetch-control" content="on">' . "\n";
		echo apply_filters( 'webby_performance/seo/resource_hints/print_tag', $meta );
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

		$wp_customize->add_section(
			$this->section_id,
			[
				'title'    => __( 'Resource Hints', 'webby-performance' ),
				'priority' => $this->section_priority,
				'panel'    => 'webby_performance_settings',
			]
		);

		$default_options = $this->default_options;

		$wp_customize->add_setting(
			$this->options_name . '[dns_prefetch]',
			[
				'default'           => $default_options['dns_prefetch'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => '',
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[dns_prefetch]',
			[
				'label'       => __( 'DNS-Prefetch', 'webby-performance' ),
				'section'     => $this->section_id,
				'type'        => 'textarea',
				'description' =>
					'CSV Format: href (host Only, excluding protocol)<br>' .
					sprintf(
						__( 'See <a href="%1$s" target="_blank">Specification</a>.', 'webby-performance' ),
						'https://html.spec.whatwg.org/multipage/links.html#link-type-dns-prefetch'
					),
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[preconnect]',
			[
				'default'           => $default_options['preconnect'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => '',
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[preconnect]',
			[
				'label'       => __( 'Preconnect', 'webby-performance' ),
				'section'     => $this->section_id,
				'type'        => 'textarea',
				'description' =>
					'CSV Format: href<br>' .
					sprintf(
						__( 'See <a href="%1$s" target="_blank">Specification</a>.', 'webby-performance' ),
						'https://html.spec.whatwg.org/multipage/links.html#link-type-preconnect'
					),
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[prefetch]',
			[
				'default'           => $default_options['prefetch'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => '',
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[prefetch]',
			[
				'label'       => __( 'Prefetch', 'webby-performance' ),
				'section'     => $this->section_id,
				'type'        => 'textarea',
				'description' =>
					'CSV Format: href, as, destination (Optional except URL)<br>' .
					sprintf(
						__( 'See <a href="%1$s" target="_blank">Specification</a>.', 'webby-performance' ),
						'https://html.spec.whatwg.org/multipage/links.html#link-type-prefetch'
					),
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[prerender]',
			[
				'default'           => $default_options['prerender'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => '',
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[prerender]',
			[
				'label'       => __( 'Prerender', 'webby-performance' ),
				'section'     => $this->section_id,
				'type'        => 'textarea',
				'description' =>
					'CSV Format: href<br>' .
					sprintf(
						__( 'See <a href="%1$s" target="_blank">Specification</a>.', 'webby-performance' ),
						'https://html.spec.whatwg.org/multipage/links.html#link-type-prerender'
					),
			]
		);
	}
}
