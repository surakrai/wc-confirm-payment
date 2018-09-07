<?php

/**
 * Class Woocommerce_Confirm_Payment_Customer_Email_Completed_Payment file.
 *
 * @package WooCommerce\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Woocommerce_Confirm_Payment_Customer_Email_Completed_Payment', false ) ) :

  /**
   * Customer Processing Order Email.
   *
   * An email sent to the customer when a new order is paid for.
   *
   * @class       Woocommerce_Confirm_Payment_Customer_Email_Completed_Payment
   * @version     2.0.0
   * @package     WooCommerce/Classes/Emails
   * @extends     WC_Email
   */
  class Woocommerce_Confirm_Payment_Customer_Email_Completed_Payment extends WC_Email {

    /**
     * Constructor.
     */
    public function __construct() {
      $this->id             = 'wcp_completed_payment';
      $this->customer_email = true;

      $this->title          = __( 'Completed payment', 'woocommerce-confirm-payment' );
      $this->description    = __( 'This is an order notification sent to customers containing order details after admin approved payment.', 'woocommerce' );
      $this->template_html  = 'emails/customer-processing-order.php';
      $this->template_plain = 'emails/plain/customer-processing-order.php';
      $this->placeholders   = array(
        '{site_title}'   => $this->get_blogname(),
        '{order_date}'   => '',
        '{order_number}' => '',
      );

      // Triggers for this email.
      add_action( 'woocommerce_order_status_checking_payment_to_processing_notification', array( $this, 'trigger' ), 10, 2 );


      // Call parent constructor.
      parent::__construct();
    }

    /**
     * Get email subject.
     *
     * @since  3.1.0
     * @return string
     */
    public function get_default_subject() {
      return __( '[{site_title}] Your payment order ({order_number}) is success', 'woocommerce-confirm-payment' );
    }

    /**
     * Get email heading.
     *
     * @since  3.1.0
     * @return string
     */
    public function get_default_heading() {
      return __( 'Thank you for your payment.', 'woocommerce-confirm-payment' );
    }

    /**
     * Trigger the sending of this email.
     *
     * @param int            $order_id The order ID.
     * @param WC_Order|false $order Order object.
     */
    public function trigger( $order_id, $order = false ) {
      $this->setup_locale();

      if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
        $order = wc_get_order( $order_id );
      }

      if ( is_a( $order, 'WC_Order' ) ) {
        $this->object                         = $order;
        $this->recipient                      = $this->object->get_billing_email();
        $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
        $this->placeholders['{order_number}'] = $this->object->get_order_number();
      }

      if ( $this->is_enabled() && $this->get_recipient() ) {
        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
      }

      $this->restore_locale();
    }

    /**
     * Get content html.
     *
     * @access public
     * @return string
     */
    public function get_content_html() {
      return wc_get_template_html(
        $this->template_html, array(
          'order'         => $this->object,
          'email_heading' => $this->get_heading(),
          'sent_to_admin' => false,
          'plain_text'    => false,
          'email'         => $this,
        )
      );
    }

    /**
     * Get content plain.
     *
     * @access public
     * @return string
     */
    public function get_content_plain() {
      return wc_get_template_html(
        $this->template_plain, array(
          'order'         => $this->object,
          'email_heading' => $this->get_heading(),
          'sent_to_admin' => false,
          'plain_text'    => true,
          'email'         => $this,
        )
      );
    }

  }

endif;
