<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.surakrai.com/
 * @since      0.9.0
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/admin
 * @author     Surakrai  <surakraisam@gmail.com>
 */
class Woocommerce_Confirm_Payment_Email{

  /**
   * The ID of this plugin.
   *
   * @since    0.9.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    0.9.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * bank_accounts
   *
   * @var array
   */
  private $bank_accounts = array();

  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;

  /**
   * Initialize the class and set its properties.
   *
   * @since    0.9.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct( $plugin_name, $version ) {

    $this->plugin_name     = $plugin_name;
    $this->version         = $version;
    $this->options         = get_option( 'wcp_option' );
    $this->bank_accounts   = get_option( 'wcp_bank_accounts' );

  }

  public function register_email($emails) {

    require_once( 'class-woocommerce-confirm-payment-email-new-payment.php' );
    require_once( 'class-woocommerce-confirm-payment-customer-email-completed-payment.php' );
    require_once( 'class-woocommerce-confirm-payment-customer-email-cancelled-payment.php' );

    $emails['WCP_Email_New_Payment']                = new Woocommerce_Confirm_Payment_Email_New_Payment();
    $emails['WCP_Customer_Email_Completed_Payment'] = new Woocommerce_Confirm_Payment_Customer_Email_Completed_Payment();
    $emails['WCP_Customer_Email_Cancelled_Payment'] = new Woocommerce_Confirm_Payment_Customer_Email_Cancelled_Payment();

    return $emails;

  }

  public function add_woocommerce_email_actions( $actions ){

    $actions[] = 'woocommerce_order_status_on-hold_to_checking_payment';
    $actions[] = 'woocommerce_order_status_checking_payment_to_processing';
    $actions[] = 'woocommerce_order_status_checking_payment_to_on-hold';

    return $actions;

  }

  /**
   * Show the order details table
   *
   * @param WC_Order $order         Order instance.
   * @param bool     $sent_to_admin If should sent to admin.
   * @param bool     $plain_text    If is plain text email.
   * @param string   $email         Email address.
   */
  public function payment_details( $order, $sent_to_admin = false, $plain_text = false, $email = '' ) {

    if ( $plain_text ) {
      wc_get_template(
        'emails/plain/email-payment-detail.php', array(
          'order'         => $order,
          'sent_to_admin' => $sent_to_admin,
          'plain_text'    => $plain_text,
          'email'         => $email,
        ), '', plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
      );
    } else {
      wc_get_template(
        'emails/email-payment-detail.php', array(
          'order'         => $order,
          'sent_to_admin' => $sent_to_admin,
          'plain_text'    => $plain_text,
          'email'         => $email,
        ), '', plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
      );
    }

  }

}