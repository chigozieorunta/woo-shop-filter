<?php
/**
 * Plugin class.
 *
 * @package WooShopFilter
 */

namespace WooShopFilter;

/**
 * WordPress plugin interface.
 */
class Plugin {

	/**
	 * Plugin's singleton instance
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Setup the plugin.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_css' ] );
	}

	/**
	 * Enqueue CSS method
	 *
	 * @return void
	 */
	public function register_css() {
		wp_enqueue_style( 'woo-shop-filter', plugin_dir_url( __FILE__ ) . '../assets/css/dist/plugin.css' );
	}

	/**
	 * Plugin Entry point based on Singleton
	 *
	 * @return Plugin $plugin Instance of the plugin abstraction.
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
