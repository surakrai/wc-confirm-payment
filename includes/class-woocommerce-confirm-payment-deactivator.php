<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.surakrai.com/
 * @since      0.9.0
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.9.0
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/includes
 * @author     Surakrai  <surakraisam@gmail.com>
 */
class Woocommerce_Confirm_Payment_Deactivator {

  /**
   * Short Description. (use period)
   *
   * Long Description.
   *
   * @since    0.9.0
   */
  public static function deactivate() {

    //delete_option( 'wcp_flush_rewrite_rules' );

  }

}
