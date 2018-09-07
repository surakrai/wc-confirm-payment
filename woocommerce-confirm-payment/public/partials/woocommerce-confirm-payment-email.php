<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.surakrai.com/
 * @since      0.9.0
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/public/partials
 */
?>

<?php do_action( 'woocommerce_email_header', $email_subject ); ?>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
  <tbody>
    <tr>
      <td class="td"><?php esc_html_e( 'Name', 'woocommerce-confirm-payment' ) ?></td>
      <td class="td"><?php echo wp_kses_post( $name ); ?></td>
    </tr>
    <tr>
      <td class="td"><?php esc_html_e( 'Phone', 'woocommerce-confirm-payment' ) ?></td>
      <td class="td"><?php echo wp_kses_post( $phone ); ?></td>
    </tr>
    <tr>
      <td class="td"><?php esc_html_e( 'Date', 'woocommerce-confirm-payment' ) ?></td>
      <td class="td"><?php echo wp_kses_post( $date ); ?></td>
    </tr>
    <tr>
      <td class="td"><?php esc_html_e( 'Time', 'woocommerce-confirm-payment' ) ?></td>
      <td class="td"><?php echo wp_kses_post( $time ); ?> ?></td>
    </tr>
    <tr>
      <td class="td"><?php esc_html_e( 'Transfer amount', 'woocommerce-confirm-payment' ) ?></td>
      <td class="td"><?php echo wp_kses_post( $amount ); ?></td>
    </tr>
    <tr>
      <td class="td"><?php esc_html_e( 'Bank', 'woocommerce-confirm-payment' ) ?></td>
      <td class="td"><?php echo wp_kses_post( $bank ) ?></td>
    </tr>
    <tr>
      <td class="td"><?php esc_html_e( 'Order', 'woocommerce-confirm-payment' ) ?></td>
      <td class="td"><?php echo '<strong># '. $order_id . '</strong> ' . edit_post_link( 'View detail', '', '', $order_id );?>
      </td>
    </tr>
    <tr>
      <td class="td"><?php esc_html_e( 'Transfer slip', 'woocommerce-confirm-payment' ) ?></td>
      <td class="td"><a href="<?php echo esc_url( $slips ) ?>"><img src="<?php echo $slips_thumb; ?>"></a></td>
    </tr>
  </tbody>
</table>

<?php do_action( 'woocommerce_email_footer' ); ?>