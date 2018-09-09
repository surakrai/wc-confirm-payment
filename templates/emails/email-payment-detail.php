<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.surakrai.com/
 * @since      0.9.0
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text_align = is_rtl() ? 'right' : 'left';

do_action( 'wcp_email_before_payment_details_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php
  $payment_id    = get_post_meta( $order->get_id(), '_wcp_order_payment_id', true );
  $name          = get_the_title( $payment_id );
  $date          = get_post_meta( $payment_id, '_wcp_payment_date', true );
  $phone         = get_post_meta( $payment_id, '_wcp_payment_phone', true );
  $bank          = get_post_meta( $payment_id, '_wcp_payment_bank', true );
  $amount        = get_post_meta( $payment_id, '_wcp_payment_amount', true );
  $order_id      = $order->get_id();

  $slips         = get_the_post_thumbnail_url( $payment_id, 'large' );
  $slips_thumb   = get_the_post_thumbnail_url( $payment_id, 'medium' );
?>
<div style="margin-bottom: 40px;">
  <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
    <tbody>
      <tr>
        <th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php _e( 'Name', 'woocommerce-confirm-payment' ) ?></th>
        <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $name ?></td>
      </tr>
      <tr>
        <th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php _e( 'Phone', 'woocommerce-confirm-payment' ) ?></th>
        <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $phone ?></td>
      </tr>
      <tr>
        <th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php _e( 'Date', 'woocommerce-confirm-payment' ) ?></th>
        <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime( $date ) ); ?></td>
      <tr>
        <th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php _e( 'Transfer amount', 'woocommerce-confirm-payment' ) ?></th>
        <td class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $amount; ?></td>
      </tr>
      <tr>
        <th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php _e( 'Bank', 'woocommerce-confirm-payment' ) ?></th>
        <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $bank ?></td>
      </tr>
      <tr>
        <th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php _e( 'Order', 'woocommerce-confirm-payment' ) ?></th>
        <td class="td"><?php echo '<strong># '. $order_id . '</strong> ';?>
        </td>
      </tr>
      <tr>
        <th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php _e( 'Transfer slip', 'woocommerce-confirm-payment' ) ?></th>
        <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
          <?php echo ( $slips_thumb ? '<a href="' . $slips . '"><img src="'. $slips_thumb .'"></a>' : '-' ) ?>
        </td>
      </tr>
    </tbody>
  </table>
  <?php if( $sent_to_admin ) : ?>
    <?php printf(
      '<a href="%s">%s</a>',
        admin_url( 'edit.php?post_type=wcp_confirm_payment&action=-1&payment_id=' . $payment_id ),
        __( 'View details', 'woocommerce-confirm-payment' )
    ) ?>

  <?php endif; ?>
</div>

<?php do_action( 'wcp_email_after_payment_details_table', $order, $sent_to_admin, $plain_text, $email ); ?>