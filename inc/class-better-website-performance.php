<?php
/**
 * Better_Website_Performance class
 *
 * @package Better_Website_Performance
 *
 * @since 1.0.0
 */

namespace Better_Website_Performance;

/**
 * Core class Better_Website_Performance
 *
 * @since 1.0.0
 */
class Better_Website_Performance {
	/**
	 * Public value.
	 *
	 * @access public
	 *
	 * @var array|null $plugin_data
	 */
	public $plugin_data = [];

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'load_plugin_data' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );
		add_action( 'plugins_loaded', [ $this, 'load_class_functions' ] );

		register_uninstall_hook( BETTER_WEBSITE_PERFORMANCE, array( __CLASS__, 'uninstall' ) );
	}

	public function init() {
		add_action( 'init', [ $this, 'load_textdomain' ] );

		add_filter( 'plugin_row_meta', [ $this, 'plugin_metadata_links' ], 10, 2 );
	}

	/**
	 * Load plugin data
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_data() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->plugin_data = get_plugin_data( BETTER_WEBSITE_PERFORMANCE );
	}

	/**
	 * Load textdomain
	 *
	 * @access public
	 *
	 * @return boolean
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		return load_plugin_textdomain(
			'better-website-performance',
			false,
			plugin_dir_path( BETTER_WEBSITE_PERFORMANCE ) . 'languages'
		);
	}

	public function load_class_functions() {
		new \Better_Website_Performance\Customizer\Panel();
		new \Better_Website_Performance\Customizer\Sanitize();

		new \Better_Website_Performance\Wp_Custom_Css\Wp_Custom_Css();

		new \Better_Website_Performance\Wp_Head\Wp_Head();
		new \Better_Website_Performance\Resource_Hints\Resource_Hints();
		new \Better_Website_Performance\Preload\Preload();
		new \Better_Website_Performance\Emoji\Emoji();
		new \Better_Website_Performance\Image_Srcset\Image_Srcset();
		new \Better_Website_Performance\Jquery\Jquery();
		new \Better_Website_Performance\JavaScript\Async();
		new \Better_Website_Performance\Style\Concat();
	}

	/**
	 * Uninstall callback static class method for register_uninstall_hook
	 *
	 * @access static
	 *
	 * @return void
	 *
	 * @since 1.1.0
	 */
	public static function uninstall() {
		\Better_Website_Performance\Emoji\Emoji::uninstall();
		\Better_Website_Performance\Image_Srcset\Image_Srcset::uninstall();
		\Better_Website_Performance\JavaScript\Async::uninstall();
		\Better_Website_Performance\Jquery\Jquery::uninstall();
		\Better_Website_Performance\Preload\Preload::uninstall();
		\Better_Website_Performance\Resource_Hints\Resource_Hints::uninstall();
		\Better_Website_Performance\Style\Concat::uninstall();
		\Better_Website_Performance\Wp_Custom_Css\Wp_Custom_Css::uninstall();
		\Better_Website_Performance\Wp_Head\Wp_Head::uninstall();
	}

	/**
	 * Set links below a plugin on the Plugins page.
	 *
	 * Hooks to plugin_row_meta
	 *
	 * @see https://developer.wordpress.org/reference/hooks/plugin_row_meta/
	 *
	 * @access public
	 *
	 * @param array  $links  An array of the plugin's metadata.
	 * @param string $file   Path to the plugin file relative to the plugins directory.
	 *
	 * @return array $links
	 *
	 * @since 1.0.0
	 */
	public function plugin_metadata_links( $links, $file ) {
		if ( $file === plugin_basename( BETTER_WEBSITE_PERFORMANCE ) ) {
			$links[] = '<a href="https://github.com/sponsors/thingsym">' . __( 'Become a sponsor', 'better-website-performance' ) . '</a>';
		}

		return $links;
	}

}
