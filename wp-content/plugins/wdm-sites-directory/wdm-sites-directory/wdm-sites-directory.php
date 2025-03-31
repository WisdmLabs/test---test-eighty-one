<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://www.wisdmlabs.com
 * @since             1.0.0
 * @package           Wdm_Sites_Directory
 *
 * @wordpress-plugin
 * Plugin Name:       WDM Sites Directory
 * Plugin URI:        https://https://www.wisdmlabs.com
 * Description:       Creates a form to enter site details and allows user to view the form submissions in table format.
 * Version:           1.0.0
 * Author:            WisdmLabs
 * Author URI:        https://https://www.wisdmlabs.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wdm-sites-directory
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WDM_SITES_DIRECTORY_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wdm-sites-directory-activator.php
 */
function activate_wdm_sites_directory() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wdm-sites-directory-activator.php';
	Wdm_Sites_Directory_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wdm-sites-directory-deactivator.php
 */
function deactivate_wdm_sites_directory() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wdm-sites-directory-deactivator.php';
	Wdm_Sites_Directory_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wdm_sites_directory' );
register_deactivation_hook( __FILE__, 'deactivate_wdm_sites_directory' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wdm-sites-directory.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wdm_sites_directory() {

	$plugin = new Wdm_Sites_Directory();
	$plugin->run();

}
run_wdm_sites_directory();
