<?php
/**
 * Admin new order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-new-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.5.0
 */

 if ( ! defined( 'ABSPATH' ) ) {
 	exit;
 }

 /**
  * @hooked WC_Emails::email_header() Output the email header
  */
 do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

 <p><?php
    printf( __( 'Submitted payment. Order number #%s has been cancelled. Due to your payment is not correct.<strong>Please submit your payment again. <a href="%s">Confirm payment</a><strong>', 'woocommerce-confirm-payment' ),
    $order->get_order_number(),
    wcp_get_confirm_payment_url( $order->get_id() )
  );
 ?></p>

 <?php

 /**
  * @hooked Woocommerce_Confirm_Payment_Email::payment_details() Shows the order details table.
  * @since 1.0
  */
 do_action( 'wcp_email_payment_details', $order, $sent_to_admin, $plain_text, $email );

 /**
  * @hooked WC_Emails::email_footer() Output the email footer
  */
 do_action( 'woocommerce_email_footer', $email );