<?php
/**
 * Autoloader
 *
 * @package Webby_Performance
 * @since 1.0.0
 */

/**
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt
 * to load the \Webby_Performance\Foo\Bar class
 * from /inc/foo/class-bar.php:
 *     new \Webby_Performance\Foo\Bar;
 *
 * @param string|mixed
 * @return void
 */
spl_autoload_register( // @phpstan-ignore-line
	/**
	 * Registed autoload function
	 *
	 * @param string $class The fully-qualified class name.
	 * @return void
	 */
	function( $class ) {
		/* plugin-specific namespace prefix */
		$prefix = 'Webby_Performance\\';
		$len    = strlen( $prefix );

		if ( 0 !== strncmp( $prefix, $class, $len ) ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$relative_class = str_replace( '\\', '/', $relative_class );

		/**
		 * WordPress Naming Conventions
		 * See https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions
		 */
		$relative_class = strtolower( $relative_class );
		$relative_class = str_replace( '_', '-', $relative_class );

		if ( preg_match( '/^webby-performance$/', $relative_class ) ) {
			// load main class from /inc.
			$relative_class = preg_replace( '/^(.*)$/', 'inc/class-$1', $relative_class );
		}
		else {
			// load functions class from /inc/foo.
			$relative_class = preg_replace( '/(.*\/)(.*?)$/', 'inc//$1class-$2', $relative_class );
		}

		$path = plugin_dir_path( WEBBY_PERFORMANCE ) . $relative_class . '.php';

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
);
