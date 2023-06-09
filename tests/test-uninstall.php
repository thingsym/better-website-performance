<?php
/**
 * Class Test_Better_Website_Performance_Uninstall
 *
 * @package Better_Website_Performance
 */

/**
 * Uninstall test case.
 */
class Test_Better_Website_Performance_Uninstall extends WP_UnitTestCase {
	public $better_website_performance;

	public function setUp(): void {
		parent::setUp();
		$this->better_website_performance = new \Better_Website_Performance\Better_Website_Performance();
	}

	/**
	 * @test
	 * @group uninstall
	 */
	public function constructor() {
		$uninstall_plugins = get_option( 'uninstall_plugins' );
		$uninstall_hook = $uninstall_plugins[ plugin_basename( BETTER_WEBSITE_PERFORMANCE ) ];

		$this->assertIsArray( $uninstall_hook );
		$this->assertTrue( in_array( get_class( $this->better_website_performance ), $uninstall_hook ) );
		$this->assertTrue( in_array( 'uninstall', $uninstall_hook ) );
	}

	/**
	 * @test
	 * @group uninstall
	 */
	public function uninstall() {
		$classes = $this->_classNameProvider();

		foreach ( $classes as $class ) {
			$instance = new $class[0];
			update_option( $instance->options_name, 'uninstall' );
			$instances[] = $instance;
		}

		\Better_Website_Performance\Better_Website_Performance::uninstall();

		foreach ( $instances as $instance ) {
			$option = $this->_wpdb_get_option( $instance->options_name );
			$this->assertNull( $option );
		}
	}

	public function _classNameProvider(): array {
		return [
			[ '\Better_Website_Performance\Emoji\Emoji' ],
			[ '\Better_Website_Performance\Image_Srcset\Image_Srcset' ],
			[ '\Better_Website_Performance\JavaScript\Async' ],
			[ '\Better_Website_Performance\Jquery\Jquery' ],
			[ '\Better_Website_Performance\Preload\Preload' ],
			[ '\Better_Website_Performance\Resource_Hints\Resource_Hints' ],
			[ '\Better_Website_Performance\Style\Concat' ],
			[ '\Better_Website_Performance\Wp_Custom_Css\Wp_Custom_Css' ],
			[ '\Better_Website_Performance\Wp_Head\Wp_Head' ],
		];
	}

	public function _wpdb_get_option( $option_name ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option_name ) );
	}

}
