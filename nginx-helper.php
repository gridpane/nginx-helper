<?php
/**
 * Plugin Name:       GridPane Nginx Helper
 * Plugin URI:        https://gridpane.com
 * Description:       Cleans nginx's fastcgi/proxy cache or redis-cache whenever a post is edited/published. Also does few more things.
 * Version:           9.9.10
 * Author:            GridPane
 * Author URI:        https://gridpane.com
 * Text Domain:       gridpane-nginx-helper
 * Domain Path:       /languages
 * Requires at least: 3.0
 * Tested up to:      6.8
 *
 * @since             9.9.9
 * @package           gridpane-nginx-helper
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Base URL of plugin
 */
if ( ! defined( 'NGINX_HELPER_BASEURL' ) ) {
	define( 'NGINX_HELPER_BASEURL', plugin_dir_url( __FILE__ ) );
}

/**
 * Base Name of plugin
 */
if ( ! defined( 'NGINX_HELPER_BASENAME' ) ) {
	define( 'NGINX_HELPER_BASENAME', plugin_basename( __FILE__ ) );
}

/**
 * Base PATH of plugin
 */
if ( ! defined( 'NGINX_HELPER_BASEPATH' ) ) {
	define( 'NGINX_HELPER_BASEPATH', plugin_dir_path( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-nginx-helper-activator.php
 */
function activate_nginx_helper() {
	require_once NGINX_HELPER_BASEPATH . 'includes/class-nginx-helper-activator.php';
	Nginx_Helper_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-nginx-helper-deactivator.php
 */
function deactivate_nginx_helper() {
	require_once NGINX_HELPER_BASEPATH . 'includes/class-nginx-helper-deactivator.php';
	Nginx_Helper_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_nginx_helper' );
register_deactivation_hook( __FILE__, 'deactivate_nginx_helper' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require NGINX_HELPER_BASEPATH . 'includes/class-nginx-helper.php';

add_action( 'load-plugins.php', 'add_plugin_details_filter' );
add_action( 'load-update-core.php', 'add_plugin_details_filter' );

function add_plugin_details_filter() {
	add_filter( 'plugin_row_meta', 'remove_plugin_view_details_link', 10, 2 );
}

function remove_plugin_view_details_link( $plugin_meta, $plugin_file ) {
	$target_plugin = 'nginx-helper/nginx-helper.php';

	// Early return if not the target plugin
	if ( $plugin_file !== $target_plugin ) {
		return $plugin_meta;
	}

	foreach ( $plugin_meta as $index => $meta ) {
		if ( strpos( $meta, 'View details' ) !== false ) {
			unset( $plugin_meta[$index] );
			break;
		}
	}

	return $plugin_meta;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_nginx_helper() {

	global $nginx_helper;

	$nginx_helper = new Nginx_Helper();
	$nginx_helper->run();

	// Load WP-CLI command.
	if ( defined( 'WP_CLI' ) && WP_CLI ) {

		require_once NGINX_HELPER_BASEPATH . 'class-nginx-helper-wp-cli-command.php';
		\WP_CLI::add_command( 'nginx-helper', 'Nginx_Helper_WP_CLI_Command' );

	}

}
run_nginx_helper();
