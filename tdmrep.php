<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.pierrevieville.fr
 * @since             1.0.0
 * @package           Tdmrep
 *
 * @wordpress-plugin
 * Plugin Name:       TDMRep
 * Plugin URI:        https://github.com/Immolare/tdmrep
 * Description:       TDMRep integrates the TDM Reservation Protocol on WordPress to facilitate access to text and data mining rights for online content.
 * Version:           1.0.0
 * Author:            Pierre Vieville
 * Author URI:        https://www.pierrevieville.fr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tdmrep
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
define( 'TDMREP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tdmrep-activator.php
 */
function activate_tdmrep() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tdmrep-activator.php';
	Tdmrep_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tdmrep-deactivator.php
 */
function deactivate_tdmrep() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tdmrep-deactivator.php';
	Tdmrep_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tdmrep' );
register_deactivation_hook( __FILE__, 'deactivate_tdmrep' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tdmrep.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tdmrep() {

	$plugin = new Tdmrep();
	$plugin->run();

}
run_tdmrep();
