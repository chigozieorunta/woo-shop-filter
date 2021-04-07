<?php
/**
 * Plugin Name: Woo Shop Filter
 * Plugin URI:  https://github.com/chigozieorunta/woo-shop-filter
 * Description: A simple AJAX powered WooCommerce shop filter.
 * Version:     0.1.0
 * Author:      Chigozie Orunta
 * Author URI:  https://github.com/chigozieorunta
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woo-shop-filter
 * Domain Path: /languages
 *
 * @package WooShopFilter
 */

// Support for site-level autoloading.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

\WooShopFilter\Plugin::init();
