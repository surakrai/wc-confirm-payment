<?php

/**
 * Class Woocommerce_Confirm_Payment_Email_New_Payment file.
 *
 * @package WooCommerce\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Woocommerce_Confirm_Payment_Email_New_Payment', false ) ) :

  /**
   * Customer Processing Order Email.
   *
   * An email sent to the customer when a new order is paid for.
   *
   * @class       Woocommerce_Confirm_Payment_Email_New_Payment
   * @version     2.0.0
   * @package     WooCommerce/Classes/Emails
   * @extends     WC_Email
   */
  class Woocommerce_Confirm_Payment_Email_New_Payment extends WC_Email {

    /**
     * Constructor.
     */
    public function __construct() {
      $this->id             = 'wcp_new_payment';
      $this->title          = __( 'New confirm payment', 'woocommerce-confirm-payment' );
      $this->description    = __( 'This is a payment notification sent to admin containing payment details after customer confirm payment.', 'woocommerce-confirm-payment' );
      $this->template_html  = 'emails/admin-new-payment.php';
      $this->template_plain = 'emails/plain/admin-new-payment.php';
      $this->template_base  = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/';
      $this->placeholders   = array(
        '{site_title}'   => $this->get_blogname(),
        '{order_date}'   => '',
        '{order_number}' => '',
      );

      // Triggers for this email.

      add_action( 'woocommerce_order_status_on-hold_to_checking_payment_notification', array( $this, 'trigger' ), 10, 2 );

      // Call parent constructor.
      parent::__construct();

      // Other settings.
      $this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );

    }

    /**
     * Get email subject.
     *
     * @since  3.1.0
     * @return string
     */
    public function get_default_subject() {
      return __( '[{site_title}] Confirm payment order {order_number}', 'woocommerce-confirm-payment' );
    }

    /**
     * Get email heading.
     *
     * @since  3.1.0
     * @return string
     */
    public function get_default_heading() {
      return __( 'Customer Confirm payment', 'woocommerce-confirm-payment' );
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
          'sent_to_admin' => true,
          'plain_text'    => false,
          'email'         => $this,
        ), '', $this->template_base

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
          'sent_to_admin' => true,
          'plain_text'    => true,
          'email'         => $this,
        ), '', $this->template_base
      );
    }

    /**
     * Initialise settings form fields.
     */
    public function init_form_fields() {
      $this->form_fields = array(
        'enabled'    => array(
          'title'   => __( 'Enable/Disable', 'woocommerce' ),
          'type'    => 'checkbox',
          'label'   => __( 'Enable this email notification', 'woocommerce' ),
          'default' => 'yes',
        ),
        'recipient'  => array(
          'title'       => __( 'Recipient(s)', 'woocommerce' ),
          'type'        => 'text',
          /* translators: %s: WP admin email */
          'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'woocommerce' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
          'placeholder' => '',
          'default'     => get_option( 'admin_email' ),
          'desc_tip'    => true,
        ),
        'subject'    => array(
          'title'       => __( 'Subject', 'woocommerce' ),
          'type'        => 'text',
          'desc_tip'    => true,
          /* translators: %s: list of placeholders */
          'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
          'placeholder' => $this->get_default_subject(),
          'default'     => '',
        ),
        'heading'    => array(
          'title'       => __( 'Email heading', 'woocommerce' ),
          'type'        => 'text',
          'desc_tip'    => true,
          /* translators: %s: list of placeholders */
          'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
          'placeholder' => $this->get_default_heading(),
          'default'     => '',
        ),
        'email_type' => array(
          'title'       => __( 'Email type', 'woocommerce' ),
          'type'        => 'select',
          'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
          'default'     => 'html',
          'class'       => 'email_type wc-enhanced-select',
          'options'     => $this->get_email_type_options(),
          'desc_tip'    => true,
        ),
      );
    }
  }

endif;
