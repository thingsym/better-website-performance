<?php
/**
 * Preload
 *
 * @package Better_Website_Performance
 * @since 1.0.0
 */

namespace Better_Website_Performance\Preload;

/**
 * class Preload
 *
 * @since 1.0.0
 */
class Preload {
	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $section_id
	 */
	public $section_id = 'better_website_performance_preload';

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
	public $options_name = 'better_website_performance_preload_options';

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
		'preload' => '',
	];

	public function __construct() {
		add_action( 'customize_register', [ $this, 'customizer' ] );
		add_filter( 'wp_preload_resources', [ $this, 'add_preload' ] );
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
			return apply_filters( 'better_website_performance/preload/get_options', $options, $this->type, $default_options );
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
			return apply_filters( 'better_website_performance/preload/get_option', $options[ $option_name ], $option_name, $this->type, $default_options );
		}
		else {
			return null;
		}
	}

	public function add_preload( $preload_resources ) {
		$option = $this->get_options( 'preload' );

		$urls = preg_split( '/\R/', $option, -1, PREG_SPLIT_NO_EMPTY );

		if ( empty( $urls ) ) {
			return $preload_resources;
		}

		foreach ( $urls as $line ) {
			$param = explode( ',', $line );

			$mapped_param = array_map(
				function( $el ) {
					return trim( $el );
				},
				$param
			);

			$resources = [];

			if ( ! empty( trim( $mapped_param[0] ) ) ) {
				$resources['href'] = $mapped_param[0];
			}
			if ( ! empty( $mapped_param[1] ) ) {
				$resources['as'] = $mapped_param[1];
			}
			if ( ! empty( $mapped_param[2] ) ) {
				$resources['crossorigin'] = $mapped_param[2];
			}
			if ( ! empty( $mapped_param[3] ) ) {
				$resources['type'] = $mapped_param[3];
			}

			$preload_resources[] = $resources;
		}

		return $preload_resources;
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
				'title'    => __( 'Preload', 'better-website-performance' ),
				'priority' => $this->section_priority,
				'panel'    => 'better_website_performance_settings',
			]
		);

		$default_options = $this->default_options;

		$wp_customize->add_setting(
			$this->options_name . '[preload]',
			[
				'default'           => $default_options['preload'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => '',
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[preload]',
			[
				'label'       => __( 'Preload', 'better-website-performance' ),
				'section'     => $this->section_id,
				'type'        => 'textarea',
				'description' =>
					'CSV Format: href, as, destination, MIME type (Optional except href)<br>' .
					sprintf(
						__( 'See <a href="%1$s" target="_blank">Specification</a>.', 'better-website-performance' ),
						'https://html.spec.whatwg.org/multipage/links.html#link-type-preload'
					),
			]
		);

	}
}
