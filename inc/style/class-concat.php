<?php
/**
 * Concat Style control
 *
 * @package Better_Website_Performance
 * @since 1.0.0
 */

namespace Better_Website_Performance\Style;

/**
 * class JavaScript
 *
 * @since 1.0.0
 */
class Concat {
	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $section_id
	 */
	public $section_id = 'better_website_performance_concat_style';

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
	public $options_name = 'better_website_performance_concat_style_options';

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
		'loading' => 'default',
		'minify'  => false,
		'exclude' => '',
	];

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var string $concat_css
	 */
	public $concat_css = '';

	/**
	 * Public variable.
	 *
	 * @access public
	 *
	 * @var array $exclude_handles
	 */
	public $exclude_handles = [];

	public function __construct() {
		add_action( 'init', [ $this, 'setup_exclude_style' ] );
		add_action( 'customize_register', [ $this, 'customizer' ] );
		add_action( 'style_loader_tag', [ $this, 'concat_style_tag' ], 10, 3 );
		add_action( 'wp_footer', [ $this, 'print_concat_style' ], 10 );
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
			return apply_filters( 'better_website_performance/concat_style/get_options', $options, $this->type, $default_options );
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
			return apply_filters( 'better_website_performance/concat_style/get_option', $options[ $option_name ], $option_name, $this->type, $default_options );
		}
		else {
			return null;
		}
	}

	public function concat_style_tag( $tag, $handle, $src ) {
		if ( is_admin() || did_action( 'login_head' ) ) {
			return $tag;
		}

		if ( $this->validate_exclude_style( $handle ) ) {
			return $tag;
		}

		$loading = $this->get_options( 'loading' );

		if ( $loading === 'default' ) {
			return $tag;
		}

		$minify = $this->get_options( 'minify' );
		$output = '';

		if ( $loading === 'inline' || $loading === 'concat' ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$output = file_get_contents( $src );
			if ( ! $output ) {
				return $tag;
			}

			if ( $minify ) {
				$output = $this->minify( $output );
			}
		}

		if ( $loading === 'concat' ) {
			$this->concat_css .= $output;
			return '';
		}

		if ( $loading === 'inline' ) {
			$edited_tag = sprintf(
				"<style id='better-website-performance-%s-inline-css'>\n%s\n</style>\n",
				$handle,
				$output
			);

			return apply_filters( 'better_website_performance/concat_style/edited_tag', $edited_tag, $tag, $handle, $src );
		}

		return $tag;
	}

	public function print_concat_style() {
		if ( is_admin() || did_action( 'login_head' ) ) {
			return;
		}

?>
<style id='better-website-performance-concat-styles'>
<?php
// Note that esc_html() cannot be used because `div &gt; span` is not interpreted properly.
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
echo strip_tags( $this->concat_css );
?>

</style>
<?php
	}

	public function minify( $css ) {
		$minified = mb_ereg_replace( '\t', '', $css );
		$minified = mb_ereg_replace( ' +', ' ', $minified );
		$minified = mb_ereg_replace( ' :', ':', $minified );
		$minified = mb_ereg_replace( ': ', ':', $minified );
		$minified = mb_ereg_replace( ' ;', ';', $minified );
		$minified = mb_ereg_replace( '{ ', '{', $minified );
		$minified = mb_ereg_replace( ' {', '{', $minified );
		$minified = mb_ereg_replace( '\r\n|\r|\n', '', $minified );
		$minified = mb_ereg_replace( '; ', ';', $minified );

		return $minified;
	}

	public function validate_exclude_style( $handle ) {
		return in_array( $handle, $this->exclude_handles, true );
	}

	public function setup_exclude_style() {
		$option  = $this->get_options( 'exclude' );
		$handles = preg_split( '/\R/', $option, -1, PREG_SPLIT_NO_EMPTY );

		$this->exclude_handles = apply_filters( 'better_website_performance/concat_style/setup_exclude', $handles );
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
				'title'    => __( 'Style Sheet', 'better-website-performance' ),
				'priority' => $this->section_priority,
				'panel'    => 'better_website_performance_settings',
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[loading]',
			[
				'default'           => $default_options['loading'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_radio' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[loading]',
			[
				'label'   => __( 'Loading type', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'radio',
				'choices' => [
					'default' => __( 'Default: Load external css file', 'better-website-performance' ),
					'inline'  => __( 'Inline each stylesheet', 'better-website-performance' ),
					'concat'  => __( 'Inline concated Stylesheets', 'better-website-performance' ),
				],
			]
		);

		$wp_customize->add_setting(
			$this->options_name . '[minify]',
			[
				'default'           => $default_options['minify'],
				'type'              => $this->type,
				'capability'        => $this->capability,
				'sanitize_callback' => [ 'Better_Website_Performance\Customizer\Sanitize', 'sanitize_checkbox_boolean' ],
			]
		);

		$wp_customize->add_control(
			$this->options_name . '[minify]',
			[
				'label'   => __( 'Enable minify', 'better-website-performance' ),
				'section' => $this->section_id,
				'type'    => 'checkbox',
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
				'label'       => __( 'Exclude', 'better-website-performance' ),
				'section'     => $this->section_id,
				'type'        => 'textarea',
				'description' => __( 'Enter handle names to exclude.', 'better-website-performance' ),
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
		delete_option( 'better_website_performance_concat_style_options' );
	}

}
