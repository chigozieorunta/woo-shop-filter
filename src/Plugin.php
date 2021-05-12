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
		add_action( 'wp_enqueue_scripts', [ $this, 'woo_shop_filter_css' ] );
		add_action( 'woocommerce_before_shop_loop', [ $this, 'get_form' ] );
		add_action( 'woocommerce_product_query', [ $this, 'woo_shop_filter_query' ] );

		add_shortcode( 'woo-shop-filter-search', 'woo_shop_filter_search' );
		add_shortcode( 'woo-shop-filter-listing', 'woo_shop_filter_listing' );

		add_action( 'admin_menu', [ $this, 'woo_shop_filter_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'woo_shop_filter_admin_css' ] );
	}

	/**
	 * Register a custom plugin menu.
	 *
	 * @return void
	 */
	public function woo_shop_filter_page() {
		add_menu_page(
			__( 'Woo Shop Filter', 'wsf' ),
			__( 'Woo Shop Filter', 'wsf' ),
			'manage_options',
			'woo-shop-filter',
			[ $this, 'woo_shop_filter_html' ],
			'dashicons-database'
		);
	}

	/**
	 * Display HTML for menu page
	 *
	 * @return void
	 */
	public function woo_shop_filter_html() {
		ob_start();
		readfile( __DIR__ . '/../woo-shop-filter.html' );
	}

	/**
	 * Enqueue CSS method
	 *
	 * @return void
	 */
	public function woo_shop_filter_css() {
		wp_enqueue_style( 'woo-shop-filter', plugin_dir_url( __FILE__ ) . '../assets/css/dist/plugin.css' );
	}

	/**
	 * Enqueue Admin CSS method
	 *
	 * @return void
	 */
	public function woo_shop_filter_admin_css() {
		wp_enqueue_style( 'woo-shop-filter-admin', plugin_dir_url( __FILE__ ) . '../assets/css/dist/admin.css' );
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
	 * @param array $arg Custom field data.
	 *
	 * @return string
	 */
	public function get_select( $arg ) {
		$results = $this->get_custom_fields_data( $arg );

		foreach ( $results as $result ) {
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
	 * @return void
	 */
	public function get_form() {
		$form = sprintf(
			'<form class="woo-shop-filter" method="POST" action="./">%1$s%2$s%3$s%4$s</form>',
			$this->get_select( 'brand' ),
			$this->get_select( 'model' ),
			$this->get_select( 'year' ),
			$this->get_button()
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

	/**
	 * Listing Shortcode
	 *
	 * @return string
	 */
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

	/**
	 * Shop Query
	 *
	 * @param string $query search query.
	 *
	 * @return void
	 */
	public function woo_shop_filter_query( $query ) {
		$meta_query = $query->get( 'meta_query' );

		if ( $_POST['brand'] ) {
			$meta_query[] = array(
				'key'     => 'brand',
				'value'   => $_POST['brand'],
				'compare' => '='
			);
		}

		if ( $_POST['model'] ) {
			$meta_query[] = array(
				'key'     => 'model',
				'value'   => $_POST['model'],
				'compare' => '='
			);
		}

		if ( $_POST['year'] ) {
			$meta_query[] = array(
				'key'     => 'year',
				'value'   => $_POST['year'],
				'compare' => '='
			);
		}

		$query->set( 'meta_query', $meta_query );
	}

	/**
	 * Get listing
	 *
	 * @return void
	 */
	public function get_listing() {

		if ( isset( $_POST['brand'] ) ) {
			$meta_query[] = array(
				'key'     => 'brand',
				'value'   => $_POST['brand'],
				'compare' => '=',
			);
		}

		if ( isset( $_POST['model'] ) ) {
			$meta_query[] = array(
				'key'     => 'model',
				'value'   => $_POST['model'],
				'compare' => '=',
			);
		}

		if ( isset( $_POST['year'] ) ) {
			$meta_query[] = array(
				'key'     => 'year',
				'value'   => $_POST['year'],
				'compare' => '=',
			);
		}

		$query = new WP_Query(
			array(
				'post_type'  => 'product',
				'meta_query' => $meta_query,
			)
		);

		$posts = $query->posts;

		foreach ( $posts as $post ) {

			$_product = wc_get_product( $post->ID );

			$image = wp_get_attachment_image_src(
				get_post_thumbnail_id( $post->ID ),
				'full'
			);

			$product_link = sprintf(
				'<a href="%1$s">View More</a>',
				get_the_permalink( $post->ID )
			);

			$product_image = sprintf(
				'<div><a href="%2$s"><img src="%1$s"></a></div>',
				$image[0],
				get_the_permalink( $post->ID )
			);

			$product_excerpt = sprintf(
				'<div>%1$s</div>%2$s',
				get_the_excerpt( $post->ID ),
				$product_link
			);

			$product_details = sprintf(
				'<div>%3$s<h2><a href="%5$s">%1$s</a></h2>%4$s%2$s</div>',
				$_product->get_title(),
				$product_excerpt,
				$_product->get_sku(),
				$_product->get_rating_html(),
				get_the_permalink( $post->ID )
			);

			$product_price = sprintf(
				'<div><em>%2$s</em><h2>%1$s</h2>%3$s</div>',
				$_product->get_price_html(),
				$_product->get_sku() ? '&nbsp;' : '',
				'<p>We at Bakers Locksmith are GSA certified technicians trained at LSI (Lockmasters Security Institute).</p>
				<p>Call the store at:<br/>937-328-LOCK (5625)<br/>For Northern Locations: 937-492-2235<br/>bakerslocksmith@icloud.com</p>'
			);

			$products .= sprintf(
				'<section class="shop-filter">%1$s%2$s%3$s</section>',
				$product_image,
				$product_details,
				$product_price
			);

		}

		$product_count = sprintf(
			'<span>Showing %1$s Products</span>',
			count($posts)
		);

		$product_all = sprintf(
			'<span><a href="%1$s">All Products</a></span>',
			wc_get_page_permalink( 'shop' )
		);

		$products = sprintf(
			'<section class="shop-filter-nav">%1$s%2$s</section>%3$s',
			$product_count,
			$product_all,
			$products
		);

		echo $products;
	}
}
