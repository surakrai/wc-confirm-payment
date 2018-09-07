<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.surakrai.com/
 * @since      0.9.0
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.9.0
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/includes
 * @author     Surakrai  <surakraisam@gmail.com>
 */
class Woocommerce_Confirm_Payment_i18n {


  /**
   * Load the plugin text domain for translation.
   *
   * @since    0.9.0
   */
  public function load_plugin_textdomain() {

    load_plugin_textdomain(
      'woocommerce-confirm-payment',
      false,
      dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
    );

  }



}
