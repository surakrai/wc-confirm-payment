<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.surakrai.com/
 * @since      0.9.0
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/public
 * @author     Surakrai  <surakraisam@gmail.com>
 */
class Woocommerce_Confirm_Payment_History {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    0.9.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name     = $plugin_name;
		$this->version         = $version;

	}

  public function woocommerce_menu_item( $menu_item ){

    $menu_item = array_slice( $menu_item, 0, 3, true )
    + array( $this->plugin_name => $this->woocommerce_endpoints_title() )
    + array_slice( $menu_item, 3, NULL, true );

    return $menu_item;

  }

  public function woocommerce_add_endpoints() {

    add_rewrite_endpoint( $this->plugin_name, EP_ROOT | EP_PAGES );

  }

  public function woocommerce_endpoints_title() {

    return __( 'Payment History', 'woocommerce-confirm-payment' );

  }

  public function woocommerce_get_query_vars( $endpoints ) {

    $endpoints[$this->plugin_name] = 'payment-history';

    return $endpoints;

  }

  public function woocommerce_account_payment_history( $current_page ) {

    $current_page = empty( $current_page ) ? 1 : absint( $current_page );

    $args = array(
      'post_type'      => 'wcp_confirm_payment',
      'meta_key'       => '_wcp_payment_user_id',
      'meta_value'     => get_current_user_id(),
      'posts_per_page' => get_option( 'posts_per_page' ),
      'paged'          => $current_page
    );

    $the_query = new WP_Query( $args );

    wc_get_template(
      'partials/woocommerce-confirm-payment-history.php',
      array(
        'current_page'    => absint( $current_page ),
        'payments'        => $the_query,
      ),
      '', plugin_dir_path( dirname( __FILE__ ) ) . 'public/'
    );

  }


}