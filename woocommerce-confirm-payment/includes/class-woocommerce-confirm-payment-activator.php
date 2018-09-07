<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.surakrai.com/
 * @since      0.9.0
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.9.0
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/includes
 * @author     Surakrai  <surakraisam@gmail.com>
 */
class Woocommerce_Confirm_Payment_Activator {

  /**
   * Short Description. (use period)
   *
   * Long Description.
   *
   * @since    0.9.0
   */
  public static function activate() {

    $option = get_option( 'wcp_option' );

    if ( empty( $option['confirm_page'] ) ) {

      $page_id = wp_insert_post( array(
        'post_title'    => wp_strip_all_tags( __( 'Confirm Payment' ) ),
        'post_content'  => '[wcp_confirm_payment_form]',
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'     => 'page',
      ) );

      $option['confirm_page'] = $page_id;

      update_option( 'wcp_option', $option );

    }

    if ( ! function_exists( 'WC' ) ) return;

    update_option( 'wcp_flush_rewrite_rules', 'yes' );

    if ( wcp_get_gateway_bacs() ) {
      wcp_get_gateway_bacs()->update_option( 'enabled', 'yes' );
    }


    $bacs_accounts     = get_option('woocommerce_bacs_accounts');
    $wcp_bank_accounts = get_option('wcp_bank_accounts');

    if ( ! empty( $bacs_accounts ) && empty( $wcp_bank_accounts ) ) {


      foreach ( $bacs_accounts as $bacs_account ) {

        $accounts[] = array(
          'bank_logo'      => '',
          'bank_name'      => $bacs_account['bank_name'],
          'account_number' => $bacs_account['account_number'],
          'account_name'   => $bacs_account['account_name'],
        );

      }

      update_option( 'wcp_bank_accounts', $accounts );

    }

  }

}
