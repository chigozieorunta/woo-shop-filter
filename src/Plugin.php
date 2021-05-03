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
	 * Custom data field
	 *
	 * @var string
	 */
	private $arg;

	/**
	 * Setup the plugin.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_css' ] );
		add_action( 'woocommerce_before_shop_loop', [ $this, 'get_form' ] );

		add_shortcode( 'woo-shop-filter-search', 'woo_shop_filter_search' );
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
	 * Get custom fields data
	 *
	 * @param string $arg Custom field name.
	 *
	 * @return array
	 */
	public function get_custom_fields_data( $arg ) {
		global $wpdb;
		$this->arg = $arg;
		$results   = $wpdb->get_results( 'SELECT DISTINCT meta_value FROM wp_postmeta WHERE meta_key LIKE "' . $arg . '"', OBJECT );

		return $results;
	}

	/**
	 * Get select input
	 *
	 * @param array $results Custom field data.
	 *
	 * @return string
	 */
	public function get_select( $results ) {
		foreach($results as $result) {
			$options .= sprintf(
				'<option name="%1$s">%1$s</option>',
				$result->meta_value
			);
		}

		$options = sprintf(
			'<option value="" selected disabled>Choose %2$s</option>%1$s',
			$options,
			$this->arg
		);

		$select = sprintf(
			'<select name="%2$s">%1$s</select>',
			$options,
			$this->arg
		);

		return $select;
	}

	/**
	 * Get button
	 *
	 * @return string
	 */
	public function get_button() {
		return '<button type="submit">Filter Products</button>';
	}

	/**
	 * Get form
	 *
	 * @return string
	 */
	public function get_form() {
		$form = sprintf(
			'<form class="woo-shop-filter" method="POST" action="./">%1$s%2$s%3$s%4$s</form>',
			get_select( 'brand' ),
			get_select( 'model' ),
			get_select( 'year' ),
			get_button()
		);

		echo $form;
	}

	/**
	 * Search Shortcode
	 *
	 * @return string
	 */
	public function woo_shop_filter_search() {
		ob_start();
		get_form();

		return ob_get_clean();
	}

	public function woo_shop_filter_listing() {
		ob_start();
		get_listing();

		return ob_get_clean();
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
