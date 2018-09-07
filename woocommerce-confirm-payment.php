<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.surakrai.com/
 * @since             0.9.0
 * @package           Woocommerce_Confirm_Payment
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce confirm payment
 * Plugin URI:        Confirm Payment
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           0.9.1
 * Author:            Surakrai
 * Author URI:        https://www.surakrai.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-confirm-payment
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
define( 'PLUGIN_NAME_VERSION', '0.9.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-confirm-payment-activator.php
 */
function activate_woocommerce_confirm_payment() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-confirm-payment-activator.php';
  Woocommerce_Confirm_Payment_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-confirm-payment-deactivator.php
 */
function deactivate_woocommerce_confirm_payment() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-confirm-payment-deactivator.php';
  Woocommerce_Confirm_Payment_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_confirm_payment' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_confirm_payment' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-confirm-payment.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.9.0
 */
function run_woocommerce_confirm_payment() {

  $plugin = new Woocommerce_Confirm_Payment();
  $plugin->run();

}
run_woocommerce_confirm_payment();