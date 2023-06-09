<?php
/**
 * Class Test_Wp_Head
 *
 * @package Better_Website_Performance
 */

/**
 * Basic test case.
 */
class Test_Wp_Head extends WP_UnitTestCase {
	public $wp_head;

	public function setUp(): void {
		parent::setUp();
		$this->wp_head = new \Better_Website_Performance\Wp_Head\Wp_Head();
	}

	public function tearDown(): void {
		delete_option( $this->wp_head->options_name );
		remove_filter( 'better_website_performance/wp_head/get_option', array( $this, '_filter_option' ) );
		remove_filter( 'better_website_performance/wp_head/get_options', array( $this, '_filter_options' ) );
		parent::tearDown();
	}

	/**
	 * @test
	 * @group Wp_Head
	 */
	public function public_variable() {
		$this->assertSame( 'better_website_performance_wp_head', $this->wp_head->section_id );
		$this->assertSame( 160, $this->wp_head->section_priority );
		$this->assertSame( 'better_website_performance_wp_head_options', $this->wp_head->options_name );
		$this->assertSame( 'option', $this->wp_head->type );
		$this->assertSame( 'manage_options', $this->wp_head->capability );

		$expected = array(
			'feed_links'                      => true,
			'feed_links_extra'                => true,
			'rsd_link'                        => true,
			'wlwmanifest_link'                => true,
			'wp_generator'                    => true,
			'rel_canonical'                   => true,
			'wp_shortlink_wp_head'            => true,
			'rest_output_link_wp_head'        => true,
			'wp_oembed_add_discovery_links'   => true,
		);
		$this->assertSame( $expected, $this->wp_head->default_options );
	}

	/**
	 * @test
	 * @group Wp_Head
	 */
	public function constructor() {
		$this->assertSame( 10, has_filter( 'customize_register', [ $this->wp_head, 'customizer' ] ) );
		$this->assertSame( 10, has_action( 'init', [ $this->wp_head, 'init' ] ) );
	}

	/**
	 * @test
	 * @group Wp_Head
	 */
	public function get_options_default() {
		$expected = array(
			'feed_links'                      => true,
			'feed_links_extra'                => true,
			'rsd_link'                        => true,
			'wlwmanifest_link'                => true,
			'wp_generator'                    => true,
			'rel_canonical'                   => true,
			'wp_shortlink_wp_head'            => true,
			'rest_output_link_wp_head'        => true,
			'wp_oembed_add_discovery_links'   => true,
		);

		$actual = $this->wp_head->get_options();

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Wp_Head
	 */
	public function get_options_case_1() {
		$expected = array(
			'feed_links'                      => false,
			'feed_links_extra'                => false,
			'rsd_link'                        => false,
			'wlwmanifest_link'                => false,
			'wp_generator'                    => false,
			'rel_canonical'                   => false,
			'wp_shortlink_wp_head'            => false,
			'rest_output_link_wp_head'        => false,
			'wp_oembed_add_discovery_links'   => false,
		);

		$options = array(
			'feed_links'                      => false,
			'feed_links_extra'                => false,
			'rsd_link'                        => false,
			'wlwmanifest_link'                => false,
			'wp_generator'                    => false,
			'rel_canonical'                   => false,
			'wp_shortlink_wp_head'            => false,
			'rest_output_link_wp_head'        => false,
			'wp_oembed_add_discovery_links'   => false,
		);

		update_option( $this->wp_head->options_name, $options );

		$actual = $this->wp_head->get_options();

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Wp_Head
	 */
	public function get_options_case_filters() {
		$expected = array(
			'feed_links'                      => false,
			'feed_links_extra'                => false,
			'rsd_link'                        => false,
			'wlwmanifest_link'                => false,
			'wp_generator'                    => false,
			'rel_canonical'                   => false,
			'wp_shortlink_wp_head'            => false,
			'rest_output_link_wp_head'        => false,
			'wp_oembed_add_discovery_links'   => false,
		);

		add_filter( 'better_website_performance/wp_head/get_options', array( $this, '_filter_options' ), 10 );

		$actual = $this->wp_head->get_options();
		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group Wp_Head
	 */
	public function get_option_case_filters() {
		add_filter( 'better_website_performance/wp_head/get_option', array( $this, '_filter_option' ), 10, 2 );

		$actual = $this->wp_head->get_options( 'feed_links' );
		$this->assertFalse( $actual );
	}

	public function _filter_options( $options ) {
		$expected = array(
			'feed_links'                      => true,
			'feed_links_extra'                => true,
			'rsd_link'                        => true,
			'wlwmanifest_link'                => true,
			'wp_generator'                    => true,
			'rel_canonical'                   => true,
			'wp_shortlink_wp_head'            => true,
			'rest_output_link_wp_head'        => true,
			'wp_oembed_add_discovery_links'   => true,
		);

		$this->assertSame( $expected, $options );

		$options = array(
			'feed_links'                      => false,
			'feed_links_extra'                => false,
			'rsd_link'                        => false,
			'wlwmanifest_link'                => false,
			'wp_generator'                    => false,
			'rel_canonical'                   => false,
			'wp_shortlink_wp_head'            => false,
			'rest_output_link_wp_head'        => false,
			'wp_oembed_add_discovery_links'   => false,
		);

		return $options;
	}

	public function _filter_option( $option, $name ) {
		$this->assertTrue( $option );
		$this->assertSame( $name, 'feed_links' );

		$option = false;
		return $option;
	}

	/**
	 * @test
	 * @group Wp_Head
	 */
	public function init_default() {
		$this->wp_head->init();

		$this->assertSame( 2, has_filter( 'wp_head', 'feed_links' ) );
		$this->assertSame( 3, has_filter( 'wp_head', 'feed_links_extra' ) );
		$this->assertSame( 10, has_filter( 'wp_head', 'rsd_link' ) );
		// wlwmanifest_link will be removed in version 6.3
		// $this->assertSame( 10, has_filter( 'wp_head', 'wlwmanifest_link' ) );
		$this->assertSame( 10, has_filter( 'wp_head', 'wp_generator' ) );
		$this->assertSame( 10, has_filter( 'wp_head', 'rel_canonical' ) );
		$this->assertSame( 10, has_filter( 'wp_head', 'wp_shortlink_wp_head' ) );
		$this->assertSame( 10, has_filter( 'wp_head', 'rest_output_link_wp_head' ) );
		$this->assertSame( 10, has_filter( 'wp_head', 'wp_oembed_add_discovery_links' ) );
	}

	/**
	 * @test
	 * @group Wp_Head
	 */
	public function init_disabled() {
		$options = array(
			'feed_links'                      => false,
			'feed_links_extra'                => false,
			'rsd_link'                        => false,
			'wlwmanifest_link'                => false,
			'wp_generator'                    => false,
			'rel_canonical'                   => false,
			'wp_shortlink_wp_head'            => false,
			'rest_output_link_wp_head'        => false,
			'wp_oembed_add_discovery_links'   => false,
		);

		update_option( $this->wp_head->options_name, $options );

		$this->wp_head->init();

		$this->assertFalse( has_filter( 'wp_head', 'feed_links' ) );
		$this->assertFalse( has_filter( 'wp_head', 'feed_links_extra' ) );
		$this->assertFalse( has_filter( 'wp_head', 'rsd_link' ) );
		$this->assertFalse( has_filter( 'wp_head', 'wlwmanifest_link' ) );
		$this->assertFalse( has_filter( 'wp_head', 'wp_generator' ) );
		$this->assertFalse( has_filter( 'wp_head', 'rel_canonical' ) );
		$this->assertFalse( has_filter( 'wp_head', 'wp_shortlink_wp_head' ), 10 );
		$this->assertFalse( has_filter( 'wp_head', 'rest_output_link_wp_head' ) );
		$this->assertFalse( has_filter( 'wp_head', 'wp_oembed_add_discovery_links' ) );
	}

	/**
	 * @test
	 * @group Wp_Head
	 */
	public function adjacent_posts_rel_link_wp_head() {
		$this->wp_head->init();

		$this->assertFalse( has_filter( 'wp_head', 'adjacent_posts_rel_link_wp_head' ) );
	}

}
