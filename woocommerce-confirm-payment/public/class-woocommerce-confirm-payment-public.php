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
class Woocommerce_Confirm_Payment_Public {

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
   * line api url
   *
   * @var string
   */
  private $line_api;

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
    $this->bank_accounts   = get_option( 'wcp_bank_accounts' );
    $this->options         = get_option( 'wcp_option' );
    $this->line_api        = 'https://notify-api.line.me/api/notify';

    $this->load_dependencies();
	}


  /**
   * Load the required dependencies for the Admin facing functionality.
   *
   * Include the following files that make up the plugin:
   *
   * - Woocommerce_Confirm_Payment_History.
   *
   *
   * @since    0.9.0
   * @access   private
   */
  private function load_dependencies() {

    /**
     * The class responsible for orchestrating the actions and filters of the
     * core plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) .  'public/class-woocommerce-confirm-payment-history.php';

  }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.9.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Confirm_Payment_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Confirm_Payment_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
    wp_enqueue_style( $this->plugin_name . '-datetimepicker', plugin_dir_url( __FILE__ ) . 'css/jquery.datetimepicker.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-confirm-payment-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.9.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Confirm_Payment_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Confirm_Payment_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

    wp_enqueue_script( $this->plugin_name . '-modernizr', plugin_dir_url( __FILE__ ) . 'js/modernizr-custom.js', array( 'jquery' ), $this->version, false );
    wp_enqueue_script( $this->plugin_name . '-jquery-form', plugin_dir_url( __FILE__ ) . 'js/jquery.form.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-confirm-payment-public.js', array( 'jquery' ), $this->version, false );

    wp_localize_script( $this->plugin_name, 'WCP', array(
      'ajaxurl'            => admin_url( 'admin-ajax.php' ),
      'check_order_nonce'  => wp_create_nonce( 'wcp_check_order_nonce' ),
      'cleave'             => plugin_dir_url( __FILE__ ) . 'js/cleave.min.js',
      'current_date'       => current_time( 'd-m-Y' )
    ));

	}

  public function add_shortcode() {

    add_shortcode( 'wcp_confirm_payment_form', array( $this, 'print_confirm_payment_form' ) );
    add_shortcode( 'wcp_confirm_payment_button', array( $this, 'print_confirm_payment_button' ) );

  }

  public function print_confirm_payment_form( $atts, $content = '' ) {

    ob_start();

    include( 'partials/woocommerce-confirm-payment-confirm-form.php' );

    $html = ob_get_contents();
    ob_end_clean();

    return $html;

  }

  public function print_confirm_payment_button( $atts, $content = '' ) {

    return sprintf(
      '<a href="%s" class="button wcp-button-confirm">%s</button>',
      empty( $atts['order_id'] ) ? wcp_get_confirm_payment_url() : wcp_get_confirm_payment_url( $atts['order_id'] ),
      __( 'Confirm payment', 'woocommerce-confirm-payment' )
    );

  }


  public function confirm_payment() {

    check_ajax_referer( 'wcp_form_nonce_action', 'wcp_form_security_nonce' );

    $errors        = array();
    $success       = '';
    $allowed_slip  = array( 'image/jpeg', 'image/gif', 'image/png', 'application/pdf' );
    $name          = sanitize_text_field( $_POST['name'] );
    $phone         = sanitize_text_field( $_POST['phone'] );
    $date          = sanitize_text_field( $_POST['date'] );
    $time          = sanitize_text_field( $_POST['time'] );
    $bank          = sanitize_text_field( $_POST['bank'] );
    $amount        = absint( $_POST['amount'] );
    $slip          = $_FILES['slip'];
    $order_id      = absint( $_POST['order'] );

    if ( ! $name )   $errors['name']   = __( 'Enter your name', 'woocommerce-confirm-payment' );
    if ( ! $phone )  $errors['phone']  = __( 'Enter your phone.', 'woocommerce-confirm-payment' );
    if ( ! $date )   $errors['date']   = __( 'Enter transfer date', 'woocommerce-confirm-payment' );
    if ( ! $time )   $errors['time']   = __( 'Enter transfer time', 'woocommerce-confirm-payment' );
    if ( ! $bank )   $errors['bank']   = __( 'Select bank transfer', 'woocommerce-confirm-payment' );
    if ( ! $amount ) $errors['amount'] = __( 'Enter amount transfer', 'woocommerce-confirm-payment' );

    if ( ! $order_id ){
      $errors['order']  = __( 'Enter order number', 'woocommerce-confirm-payment' );
    } else {
      if ( $this->check_order( $order_id ) ) {
        $errors['order'] = $this->check_order( $order_id );
      }
    }

    if ( ! $slip ){
      $errors['slip'] = __( 'Upload transfer slip', 'woocommerce-confirm-payment' );
    }else {
      if ( ! in_array( $slip['type'], $allowed_slip ) )
        $errors['slip'] = __( 'This file type is not supported. You can only upload jpg, png, gif, pdf files.', 'woocommerce-confirm-payment' );
    }

    if ( empty( $errors )  ) {

      $post_id = wp_insert_post( array(
        'post_type'         => 'wcp_confirm_payment',
        'post_title'        => wp_strip_all_tags( $name ),
        'post_status'       => 'wcp-pending_confirm',
        'post_author'       => 1,
      ) );

      update_post_meta( $post_id, '_wcp_payment_date', date_i18n( 'Y-m-d H:i', strtotime( str_replace( '/', '-', $date . ' ' . $time ) ) ) );
      update_post_meta( $post_id, '_wcp_payment_phone', $phone );
      update_post_meta( $post_id, '_wcp_payment_bank', $bank );
      update_post_meta( $post_id, '_wcp_payment_amount', $amount );
      update_post_meta( $post_id, '_wcp_payment_order_id', $order_id );
      update_post_meta( $post_id, '_wcp_payment_user_id', ( is_user_logged_in() ? get_current_user_id() : 'guest' ) );
      update_post_meta( $order_id,'_wcp_order_payment_id', $post_id );

      if ( $post_id != 0 ){

        if ( !function_exists('wp_generate_attachment_metadata') ){
          require_once(ABSPATH . "wp-admin" . '/includes/image.php');
          require_once(ABSPATH . "wp-admin" . '/includes/file.php');
          require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        }

        $attachment_id = media_handle_upload( 'slip', $post_id, array( 'post_title' => 'Payment slip #' . $order_id  ) );

        if ( is_wp_error( $attachment_id ) ) {

          $errors['attachment'] = $attachment_id->get_error_message();

          wp_delete_post( $post_id );

        }else{

          update_post_meta( $post_id,'_thumbnail_id', $attachment_id );
          update_post_meta( $attachment_id,'_wcp_payment_slip', $post_id );

          $this->payment_complete( $post_id );

          $check_icon = '<svg class="checkmark" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2"><circle class="checkmark-path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/><polyline class="checkmark-path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/></svg>';

          $success = $check_icon;
          $success .= __( 'Successfully payment confirmation!', 'woocommerce-confirm-payment'  );
        }

      }

    }

    $response = array(
      'success' => apply_filters( 'wcp_confirm_payment_success_message', $success, $check_icon ),
      'errors'  => $errors,
    );

    wp_send_json( $response );

  }

  public function ajax_check_order() {

    check_ajax_referer( 'wcp_check_order_nonce', 'security' );

    $order_id = absint( $_POST['order_id'] );

    if( ! $order_id ) return;

    $orders = wc_get_order( $order_id );

    $response = array(
      'total'   => $orders ? $orders->get_total() : '',
      'errors'  => $this->check_order( $order_id ),
    );

    wp_send_json( $response );

  }

  public function check_order( $order_id ){

    $orders = wc_get_order( $order_id );

    if ( $orders ) {

      if ( 'bacs' === $orders->get_payment_method() ) {
        if ( 'on-hold' === $orders->get_status() ) {
          return;
        }else{
          if ( 'checking_payment' === $orders->get_status() ) {
            $errors = __( 'This order has been paid. Please wait for the confirmation from the store.', 'woocommerce-confirm-payment' );
          } elseif ( 'processing' === $orders->get_status() ) {
            $errors = __( 'This order has been successfully paid.', 'woocommerce-confirm-payment' );
          } else {
            $errors = __( 'This order is not in any payment status.', 'woocommerce-confirm-payment' );
          }
        }
      } else {
        $errors = __( 'This order does not require an informed your payment.', 'woocommerce-confirm-payment' );
      }
    }else{
      $errors = __( 'Order not found', 'woocommerce-confirm-payment' );
    }

    return $errors;

  }

  public function payment_complete( $payment_id ){

    $name          = get_the_title( $payment_id );
    $date          = get_post_meta( $payment_id, '_wcp_payment_date', true );
    $phone         = get_post_meta( $payment_id, '_wcp_payment_phone', true );
    $bank          = get_post_meta( $payment_id, '_wcp_payment_bank', true );
    $amount        = get_post_meta( $payment_id, '_wcp_payment_amount', true );
    $order_id      = get_post_meta( $payment_id, '_wcp_payment_order_id', true );

    $slips         = get_the_post_thumbnail_url( $payment_id, 'large' );
    $slips_thumb   = get_the_post_thumbnail_url( $payment_id, 'medium' );

    $message .= PHP_EOL;
    $message .= '<strong>' . __( 'Name', 'woocommerce-confirm-payment' ) . ' :</strong> ' . $name . PHP_EOL;
    $message .= '<strong>' . __( 'Phone', 'woocommerce-confirm-payment' ) . ' :</strong> ' . $phone . PHP_EOL;
    $message .= '<strong>' . __( 'Date', 'woocommerce-confirm-payment' ) . ' :</strong> ' . date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime( $date ) ) . PHP_EOL;
    $message .= '<strong>' . __( 'Amount', 'woocommerce-confirm-payment' ) . ' :</strong> ' . number_format( $amount ) . PHP_EOL;
    $message .= '<strong>' .  __( 'Bank', 'woocommerce-confirm-payment' ) . ' :</strong> ' . $bank . PHP_EOL;
    $message .= '<strong>' . __( 'Order', 'woocommerce-confirm-payment' ) . ' :</strong> #' . $order_id;

    if ( $this->options['line_notification_enabled'] && $line_notify_token = $this->options['line_notify_token'] ) {

      $this->line_notify( wp_strip_all_tags( $message ), $slips_thumb, $slips, $line_notify_token );

    }

    $message = sprintf(
      '<div class="wcp-note"><h4>%s</h4><a href="%s"><img src="%s"></a> %s</div>',
      __( 'Confirm payment', 'woocommerce-confirm-payment' ),
      admin_url( 'edit.php?post_type=wcp_confirm_payment&action=-1&payment_id=' . $payment_id ),
      $slips_thumb,
      $message
    );

    $orders = wc_get_order( $order_id );
    $orders->update_status( 'checking_payment' );
    $orders->add_order_note( $message );

  }

  // https://medium.com/@nattaponsirikamonnet/มาลอง-line-notify-กันเถอะ-sticker-a2d25925d1a1

  public function line_notify( $message, $imageThumbnail, $imageFullsize, $token ){

    $queryData = array(
      'message'         => $message,
      'imageThumbnail'  => $imageThumbnail,
      'imageFullsize'   => $imageFullsize,
    );

    $queryData = http_build_query( $queryData, '', '&' );

    $headerOptions = array(
      'http' => array(
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n"
        ."Authorization: Bearer ". $token ."\r\n"
        ."Content-Length: ".strlen( $queryData )."\r\n",
        'content' => $queryData
      ),
    );

    $context = stream_context_create( $headerOptions );
    $result  = file_get_contents( $this->line_api, FALSE, $context );
    $res     = json_decode( $result );

    return $res;

  }

  /**
   * Output for the order received page.
   *
   * @param int $order_id Order ID.
   */
  public function thankyou_page( $order_id ) {

    if ( ! function_exists( 'WC' ) ) return;

    if ( wcp_get_gateway_bacs()->instructions ) {
      echo wp_kses_post( wpautop( wptexturize( wp_kses_post( wcp_get_gateway_bacs()->instructions ) ) ) );
    }

    $this->bank_details( $order_id );

  }

  /**
   * Add content to the WC emails.
   *
   * @param WC_Order $order Order object.
   * @param bool     $sent_to_admin Sent to admin.
   * @param bool     $plain_text Email format: plain text or HTML.
   */
  public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

    // Add only the email instructions
    if ( ! $sent_to_admin && 'bacs' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
      if ( wcp_get_gateway_bacs()->instructions ) {
        echo wpautop( wptexturize( wcp_get_gateway_bacs()->instructions ) ) . PHP_EOL;
      }
      $this->bank_details( $order->get_id() );
    }

  }

  /**
   * Get bank details and place into a list format.
   *
   * @param int $order_id Order ID.
   */
  private function bank_details( $order_id = '' ) {

    include( 'partials/woocommerce-confirm-payment-bank-details.php' );

  }

  public function gateway_description( $this_description, $this_id ) {

    if ( 'bacs' === $this_id ) {

      if ( ! empty( $this->bank_accounts ) ) {

        $this_description .= '<div class="wcp-our-bank">'. PHP_EOL;

        foreach ( $this->bank_accounts as $account ) {

          if ( $bank_logo = $account['bank_logo'] ) {
            $this_description .= '<div class="wcp-our-bank-item">';
            $this_description .= wp_get_attachment_image( $bank_logo, array( 40, 40 ), '', array( 'title' => $account['bank_name'] ) );
            $this_description .= '</div>' . PHP_EOL;
          }
        }
        $this_description .= '</div>';

      }

    }

    return $this_description;

  }

  // define the woocommerce_my_account_my_orders_actions callback
  function woocommerce_my_account_my_orders_actions( $actions, $the_order ) {

    if ( 'on-hold' === $the_order->get_status() && 'bacs' === $the_order->get_payment_method() ) {

      $actions['confirm_payment'] = array(
        'url'  => wcp_get_confirm_payment_url( $the_order->get_id() ),
        'name' => __( 'Confirm payment', 'woocommerce-confirm-payment' ),
      );

    }

    return $actions;

  }


  public function remove_bank_original() {

    if ( ! wcp_get_gateway_bacs() ) return false;

    remove_action( 'woocommerce_thankyou_bacs', array( wcp_get_gateway_bacs(), 'thankyou_page' ) );
    remove_action( 'woocommerce_email_before_order_table', array( wcp_get_gateway_bacs(), 'email_instructions' ), 10, 3 );

  }

  public function redirect_single_attachment(){

    global $post;

    if ( is_attachment() && get_post_meta( $post->ID, '_wcp_payment_slip', true ) ){

      wp_redirect( home_url('/'), 301 );

      exit;
    }

  }
  public function add_query_vars( $vars ){

    $vars[] = "wcp_order_id";

    return $vars;
  }

}