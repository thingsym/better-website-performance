<?php
/**
 * jQuery control
 *
 * @package Better_Website_Performance
 * @since 1.0.0
 */

namespace Better_Website_Performance\Jquery;

/**
 * class Emoji
 *
 * @since 1.0.0
 */
class Jquery {
	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $section_id
	 */
	public $section_id = 'better_website_performance_jquery';

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
	public $options_name = 'better_website_performance_jquery_options';

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
		'jquery'         => true,
		'jquery_migrate' => true,
		'in_footer'      => false,
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
			return apply_filters( 'better_website_performance/jquery/get_options', $options, $this->type, $default_options );
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
			return apply_filters( 'better_website_performance/jquery/get_option', $options[ $option_name ], $option_name, $this->type, $default_options );
		}
		else {
			return null;
		}
	}

	public function init() {
		if ( is_admin() ) {
			return;
		}

		$options = $this->get_options();

		if ( $options['jquery'] ) {
			if ( $options['jquery_migrate'] ) {
				if ( $options['in_footer'] ) {
					global $wp_scripts;

					if ( isset( $wp_scripts->registered['jquery'] ) ) {
						$jquery = $wp_scripts->registered['jquery'];
						$jquery_src = $jquery->src;
						$jquery_ver = $jquery->ver;

						wp_deregister_script( 'jquery' );
						wp_register_script( 'jquery', false, [ 'jquery-core', 'jquery-migrate' ], $jquery_ver, $options['in_footer'] );
					}

					if ( isset( $wp_scripts->registered['jquery-core'] ) ) {
						$jquery_core = $wp_scripts->registered['jquery-core'];
						$jquery_core_src = $jquery_core->src;
						$jquery_core_ver = $jquery_core->ver;

						wp_deregister_script( 'jquery-core' );
						wp_register_script( 'jquery-core', $jquery_core_src, [], $jquery_core_ver, $options['in_footer'] );
					}

					if ( isset( $wp_scripts->registered['jquery-migrate'] ) ) {
						$jquery_migrate = $wp_scripts->registered['jquery-migrate'];
						$jquery_migrate_src = $jquery_migrate->src;
						$jquery_migrate_ver = $jquery_migrate->ver;

						wp_deregister_script( 'jquery-migrate' );
						wp_register_script( 'jquery-migrate', $jquery_migrate_src, [], $jquery_migrate_ver, $options['in_footer'] );
					}
				}
			}
			else {
				global $wp_scripts;

				if ( isset( $wp_scripts->registered['jquery'] ) ) {
					$jquery = $wp_scripts->registered['jquery'];
					$jquery_src = $jquery->src;
					$jquery_ver = $jquery->ver;

					wp_deregister_script( 'jquery' );
					wp_register_script( 'jquery', false, [ 'jquery-core' ], $jquery_ver, $options['in_footer'] );
				}

				if ( isset( $wp_scripts->registered['jquery-core'] ) ) {
					$jquery_core = $wp_scripts->registered['jquery-core'];
					$jquery_core_src = $jquery_core->src;
					$jquery_core_ver = $jquery_core->ver;

					wp_deregister_script( 'jquery-core' );
					wp_register_script( 'jquery-core', $jquery_core_src, [], $jquery_core_ver, $options['in_footer'] );
				}

				wp_deregister_script( 'jquery-migrate' );
			}
		}
		else {
			wp_deregister_script( 'jquery' );
			wp_deregister_script( 'jquery-core' );
			wp_deregister_script( 'jquery-migrate' );
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
				'title'    => __( 'jQuery', 'better-website-performance' ),
				'priority' => $this->section_priority,
				'panel'    => 'better_website_performance_settings',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[jquery]',
			[
				'default'           => $default_options['jquery'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[jquery]',
			[
				'label'   => __( 'Enable jQuery', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[jquery_migrate]',
			[
				'default'           => $default_options['jquery_migrate'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[jquery_migrate]',
			[
				'label'   => __( 'Enable jQuery Migrate', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[in_footer]',
			[
				'default'           => $default_options['in_footer'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[in_footer]',
			[
				'label'   => __( 'Place jQuery in the footer', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
			]
		);
	}
}
