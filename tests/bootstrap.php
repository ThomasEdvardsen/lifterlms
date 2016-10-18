<?php
/**
 * LifterLMS Unit Tests Bootstrap
 */

class LLMS_Unit_Tests_Bootstrap {


	protected static $instance = null;

	public $wp_tests_dir;

	public $tests_dir;

	public $plugin_dir;


	/**
	 * Get singleton class instance
	 * @return   LLMS_Unit_Tests_Bootstrap
	 * @since    3.0.4
	 * @version  3.0.4
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	public function __construct() {

		ini_set( 'display_errors','on' );

		error_reporting( E_ALL );

		// Ensure server variable is set for WP email functions.
		if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
			$_SERVER['SERVER_NAME'] = 'localhost';
		}

		$this->tests_dir = dirname( __FILE__ );

		$this->plugin_dir = dirname( $this->tests_dir );

		$this->wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : '/tmp/wordpress-tests-lib';

		// load test function so tests_add_filter() is available
		require_once( $this->wp_tests_dir . '/includes/functions.php' );

		// load LLMS
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_llms' ) );

		// install LLMS
		tests_add_filter( 'setup_theme', array( $this, 'install_llms' ) );

		// load the WP testing environment
		require_once( $this->wp_tests_dir . '/includes/bootstrap.php' );

		// load LLMS testing framework
		// $this->includes();

	}


	public function load_llms() {

		require_once( $this->plugin_dir . '/lifterlms.php' );

	}


	public function install_llms() {

		// clean existing install first
		define( 'WP_UNINSTALL_PLUGIN', true );

		define( 'LLMS_REMOVE_ALL_DATA', true );

		include( $this->plugin_dir . '/uninstall.php' );

		LLMS_Install::install();

		// reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374
		$GLOBALS['wp_roles']->reinit();

		echo "Installing LifterLMS..." . PHP_EOL;

	}


	public function includes() {

	}

}

LLMS_Unit_Tests_Bootstrap::instance();
