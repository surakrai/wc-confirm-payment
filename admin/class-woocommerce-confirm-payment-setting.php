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
class Woocommerce_Confirm_Payment_Admin_Setting{

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

  public function add_plugin_page() {

    // This page will be under "Settings"

    add_submenu_page(
      'edit.php?post_type=wcp_confirm_payment',
      __( 'Woocommerce confirm payment settings', 'woocommerce-confirm-payment' ),
      __( 'Setting', 'woocommerce-confirm-payment' ),
      'manage_options',
      'wcp-setting-admin',
      array( $this, 'create_admin_page' )
    );
  }

  /**
   * Options page callback
   */
  public function create_admin_page() { ?>
    <div class="wrap wcp-wrap">
      <h1><?php _e( 'Confirm Payment Settings', 'woocommerce-confirm-payment' ) ?></h1>

      <?php settings_errors(); ?>

      <form method="post" action="options.php">
        <?php
          settings_fields( 'wcp_option_group' );
          do_settings_sections( 'wcp-setting-admin-general' );
          submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  /**
   * Register and add settings
   */
  public function page_init() {

    register_setting(
      'wcp_option_group',
      'wcp_option',
      array( $this, 'save_option' )
    );
    add_settings_section(
      'setting_general',
      '',
      '',
      'wcp-setting-admin-general'
    );

    add_settings_section(
      'setting_payment_form',
      __( 'Payment form', 'woocommerce-confirm-payment' ),
      '',
      'wcp-setting-admin-general'
    );

    add_settings_section(
      'setting_notification',
      __( 'Notification', 'woocommerce-confirm-payment' ),
      array( $this, 'setting_notification_callback' ),
      'wcp-setting-admin-general'
    );

    add_settings_field(
      'confirm_page',
      __( 'Confirm Payment Page', 'woocommerce-confirm-payment' ),
      array( $this, 'confirm_page_callback' ),
      'wcp-setting-admin-general',
      'setting_general'
    );

    add_settings_field(
      'bank_accounts',
      __( 'Bank account', 'woocommerce-confirm-payment' ),
      array( $this, 'bank_accounts_callback' ),
      'wcp-setting-admin-general',
      'setting_general'
    );

    add_settings_field(
      'transfer_slip_required',
      __( 'Transfer slip', 'woocommerce-confirm-payment' ),
      array( $this, 'checkbox_callback' ),
      'wcp-setting-admin-general',
      'setting_payment_form',
      array(
        'name'        => 'transfer_slip_required',
        'description' => __( 'Enable required field', 'woocommerce-confirm-payment' )
      )
    );

    add_settings_field(
      'show_form_on_thankyou_page',
      __( 'Payment form', 'woocommerce-confirm-payment' ),
      array( $this, 'checkbox_callback' ),
      'wcp-setting-admin-general',
      'setting_payment_form',
      array(
        'name'        => 'show_form_on_thankyou_page',
        'description' => __( 'Show form on thank you page.', 'woocommerce-confirm-payment' )
      )
    );

    add_settings_field(
      'line_notification_enabled',
      __( 'LINE Notify', 'woocommerce-confirm-payment' ),
      array( $this, 'checkbox_callback' ),
      'wcp-setting-admin-general',
      'setting_notification',
      array(
        'name'        => 'line_notification_enabled',
        'description' => __( 'Enable this line notification', 'woocommerce-confirm-payment' )
      )
    );
    add_settings_field(
      'line_notify_token',
      __( 'Line Notify Token', 'woocommerce-confirm-payment' ),
      array( $this, 'text_callback' ),
      'wcp-setting-admin-general',
      'setting_notification',
      array(
        'name' => 'line_notify_token',
        'description' => sprintf(
          __( 'Generate token <a href="%s" target="_blank">Click here<a>', 'woocommerce-confirm-payment' ),
          'https://notify-bot.line.me/'
        )
      )
    );

  }

  public function confirm_page_callback(){

    $args = array(
      'name'             => 'wcp_option[confirm_page]',
      'id'               => 'confirm_page',
      'sort_column'      => 'menu_order',
      'sort_order'       => 'ASC',
      'class'            => '',
      'echo'             => true,
      'selected'         => absint( isset( $this->options['confirm_page'] ) ? $this->options['confirm_page'] : '' ),
      'post_status'      => 'publish,private,draft',
    );

    wp_dropdown_pages( $args );

  }

  public function bank_accounts_callback(){

    include( 'partials/woocommerce-confirm-payment-admin-account-details-field.php' );

  }

  public function checkbox_callback($args) {

    printf(
      '<label><input type="checkbox" id="%s" name="wcp_option[%s]" value="1" %s /> %s</label>',
      $args['name'],
      $args['name'],
      isset( $this->options[$args['name']] ) ? checked( esc_attr( $this->options[$args['name']] ), 1, false ) : '',
      isset( $args['description'] ) ? $args['description'] : ''
    );

  }

  public function text_callback($args) {

    printf(
      '<input type="text" id="%s" name="wcp_option[%s]" value="%s" placeholder="%s" />%s',
      $args['name'],
      $args['name'],
      isset( $this->options[$args['name']] ) ? esc_attr( $this->options[$args['name']]) : '',
      isset( $args['placeholder'] ) ? $args['placeholder'] : '',
      isset( $args['description'] ) ? '<p class="description">' . $args['description'] . '</p>' : ''
    );

  }

  public function setting_payment_form_callback($args) {

    printf(
      __( 'For email notification settings, %sclick here to senting.%s', 'woocommerce-confirm-payment' ),
      '<a href="admin.php?page=wc-settings&tab=email">',
      '</a>'
    );

  }

  public function setting_notification_callback($args) {

    printf(
      __( 'For email notification settings, %sclick here to senting.%s', 'woocommerce-confirm-payment' ),
      '<a href="admin.php?page=wc-settings&tab=email">',
      '</a>'
    );

  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function save_option( $input ) {

    $accounts = array();

    if ( isset( $_POST['bank_logo'] ) && isset( $_POST['bank_name'] ) && isset( $_POST['account_name'] ) && isset( $_POST['account_number'] ) ) {

      $bank_logos      = wc_clean( wp_unslash( $_POST['bank_logo'] ) );
      $bank_names      = wc_clean( wp_unslash( $_POST['bank_name'] ) );
      $account_names   = wc_clean( wp_unslash( $_POST['account_name'] ) );
      $account_numbers = wc_clean( wp_unslash( $_POST['account_number'] ) );

      foreach ( $account_names as $i => $name ) {
        if ( ! isset( $account_names[ $i ] ) ) {
          continue;
        }

        $accounts[] = array(
          'bank_logo'      => $bank_logos[ $i ],
          'bank_name'      => $bank_names[ $i ],
          'account_number' => $account_numbers[ $i ],
          'account_name'   => $account_names[ $i ],
        );
      }
    }

    update_option( 'wcp_bank_accounts', $accounts );

    return $input;

  }

  public function add_woocommerce_email_actions( $actions ){

    $actions[] = 'woocommerce_order_status_on-hold_to_checking_payment';
    $actions[] = 'woocommerce_order_status_checking_payment_to_processing';
    $actions[] = 'woocommerce_order_status_checking_payment_to_on-hold';

    return $actions;

  }

}