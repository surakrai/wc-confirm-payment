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


if ( ! empty( $this->bank_accounts ) ) : ?>
  <section class="woocommerce-bacs-bank-details">
    <h2 class="wc-bacs-bank-details-heading"><?php _e( 'Our bank details', 'woocommerce-confirm-payment' ) ?></h2>
    <?php foreach ( $this->bank_accounts as $account ) : ?>
      <ul class="wc-bacs-bank-details order_details bacs_details">
      <?php if ( $bank_logo = $account['bank_logo'] ) { ?>
        <li class="bank_logo"><?php echo wp_get_attachment_image( $bank_logo, array( 40, 40 ) ) ?></li>
      <?php }
      if ( $bank_name = $account['bank_name'] ) { ?>
        <li class="bank_name"><?php _e( 'Bank', 'woocommerce-confirm-payment' ); ?>: <strong><?php echo $bank_name ?></strong></li>
      <?php }
      if ( $account_number = $account['account_number'] ) { ?>
        <li class="account_number"><?php  _e( 'Account number', 'woocommerce-confirm-payment' ) ?>: <strong><?php echo $account['account_number'] ?></strong></li>
      <?php }
      if ( $account_name = $account['account_name'] ) { ?>
        <li class="account_name"><?php _e( 'Account name', 'woocommerce-confirm-payment' ) ?>: <strong><?php echo $account['account_name'] ?></strong></li>
      <?php } ?>
      </ul>
    <?php endforeach; ?>
    <p><?php printf( __( 'After bank transfer, Please confirm your payment here <a href="%s">Confirm payment</a></strong>' ), wcp_get_confirm_payment_url( $order_id ) ); ?></p>
  </section>
<?php endif;