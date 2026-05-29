<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           Version_Hopper
 * @author            Sajjad Hossain Sagor <sagorh672@gmail.com>
 *
 * Plugin Name:       Version Hopper
 * Plugin URI:        https://wordpress.org/plugins/version-hopper/
 * Description:       Easily switch between plugin or theme versions from WordPress.org without hassle.
 * Version:           1.0.2
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Sajjad Hossain Sagor
 * Author URI:        https://sajjadhsagor.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       version-hopper
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_VERSION', '1.0.2' );

/**
 * Define Plugin Folders Path
 */
define( 'VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-version-hopper-activator.php
 *
 * @since    1.0.0
 */
function vhversionhopper_on_activate_version_hopper() {
	require_once VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_PATH . 'includes/class-version-hopper-activator.php';

	Version_Hopper_Activator::on_activate();
}

register_activation_hook( __FILE__, 'vhversionhopper_on_activate_version_hopper' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-version-hopper-deactivator.php
 *
 * @since    1.0.0
 */
function vhversionhopper_on_deactivate_version_hopper() {
	require_once VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_PATH . 'includes/class-version-hopper-deactivator.php';

	Version_Hopper_Deactivator::on_deactivate();
}

register_deactivation_hook( __FILE__, 'vhversionhopper_on_deactivate_version_hopper' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 *
 * @since    1.0.0
 */
require VHVERSIONHOPPER_VERSION_HOPPER_PLUGIN_PATH . 'includes/class-version-hopper.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function vhversionhopper_run_version_hopper() {
	$plugin = new Version_Hopper();

	$plugin->run();
}

vhversionhopper_run_version_hopper();
