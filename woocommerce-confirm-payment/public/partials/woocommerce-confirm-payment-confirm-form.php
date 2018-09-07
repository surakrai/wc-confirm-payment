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

$customer_orders = array();
$name            = '';
$phone           = '';

if ( is_user_logged_in() ) {

  $user  = wp_get_current_user();
  $name  = $user->first_name . ' ' .$user->last_name;
  $phone = get_user_meta( get_current_user_id(), 'billing_phone', true );

  $customer_orders = get_posts( array(
    'numberposts' => -1,
    'meta_query' => array(
      'relation' => 'AND',
      array(
        'key'     => '_customer_user',
        'value'   => get_current_user_id(),
        'compare' => '==',
      ),
      array(
        'key'     => '_payment_method',
        'value'   => 'bacs',
        'compare' => '==',
      ),
    ),
    'post_type'   => 'shop_order',
    'post_status' => 'wc-on-hold'
  ) );

} ?>

<form method="POST" enctype="multipart/form-data" class="wcp-form woocommerce">

  <?php do_action( 'woocommerce_confirm_payment_form_start' ) ?>
  <div class="wcp-form-inner">

    <div class="wcp-form-group wcp-col-2">
      <label class="wcp-form-label" for="wcp-name"><?php esc_html_e( 'Name', 'woocommerce-confirm-payment' ) ?><span class="required">*</span></label>
      <div class="wcp-form-input">
        <input type="text" class="wcp-form-control" id="wcp-name" name="name" value="<?php echo esc_attr( $name ); ?>">
      </div>
    </div>

    <div class="wcp-form-group wcp-col-2">
      <label class="wcp-form-label" for="wcp-phone"><?php esc_html_e( 'Phone', 'woocommerce-confirm-payment' ) ?><span class="required">*</span></label>
      <div class="wcp-form-input">
        <input type="tel" class="wcp-form-control" id="wcp-phone" name="phone" value="<?php echo esc_attr( $phone ); ?>">
      </div>
    </div>

    <div class="wcp-form-group wcp-col-2">
      <label class="wcp-form-label" for="wcp-order"><?php esc_html_e( 'Order', 'woocommerce-confirm-payment' ) ?><span class="required">*</span></label>
      <div class="wcp-form-input">
        <?php if ( $customer_orders ) : ?>
          <select class="wcp-form-control" id="wcp-order" name="order">
            <?php foreach ( $customer_orders as $customer_order ) :
              $orders = wc_get_order( $customer_order );
              printf(
                '<option value="%s" data-total="%s" %s>#%s (%s)</option>',
                $orders->get_id(),
                $orders->get_total(),
                selected( get_query_var( 'wcp_order_id' ), $orders->get_id(), false ),
                $orders->get_order_number(),
                 esc_html_e( 'Total', 'woocommerce-confirm-payment' ) . ' ' . $orders->get_formatted_order_total()
              );
              endforeach; ?>
          </select>
        <?php else : ?>
          <input type="number" class="wcp-form-control" id="wcp-order" name="order" value="<?php echo esc_attr( get_query_var( 'wcp_order_id' ) ); ?>">
        <?php endif; ?>
      </div>
    </div>

    <div class="wcp-form-group wcp-col-2">
      <label class="wcp-form-label" for="wcp-amount"><?php esc_html_e( 'Transfer amount', 'woocommerce-confirm-payment' ) ?><span class="required">*</span></label>
      <input type="number" class="wcp-form-control" id="wcp-amount" name="amount">
    </div>

    <div class="wcp-form-group">
      <label class="wcp-form-label"><?php esc_html_e( 'Bank transfer', 'woocommerce-confirm-payment' ) ?><span class="required">*</span></label>
      <div class="wcp-form-input wcp-form-input-radio" id="wcp-bank">
        <?php if ( !empty( $this->bank_accounts ) ) :
          foreach ( $this->bank_accounts as $account ) : ?>
            <label class="wcp-account-detail-item">
              <ul>
                <li class="wcp-account-bank">
                  <input type="radio" name="bank" value="<?php echo $account['bank_name']; ?>">
                  <?php if( $logos = wp_get_attachment_image_src( $account['bank_logo'], 'thumbnail' ) ) : ?>
                  <img src="<?php echo $logos[0] ?>">
                  <?php endif; ?>
                  <?php echo esc_html( $account['bank_name'] . ' / ' . $account['account_number'] ); ?>
                </li>
              </ul>
            </label>
          <?php endforeach;
        endif ?>
      </div>
    </div>

    <div class="wcp-form-group wcp-col-2">
      <label class="wcp-form-label" for="wcp-datetime"><?php esc_html_e( 'Transfer date', 'woocommerce-confirm-payment' ) ?><span class="required">*</span></label>
      <div class="wcp-form-input">
        <input type="date" class="wcp-form-control" id="wcp-date" name="date" max="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" placeholder="<?php esc_attr_e( 'dd/mm/yyyy', 'woocommerce-confirm-payment' ) ?>">
      </div>
    </div>

    <div class="wcp-form-group wcp-col-2">
      <label class="wcp-form-label" for="wcp-time"><?php esc_html_e( 'Transfer time', 'woocommerce-confirm-payment' ) ?><span class="required">*</span></label>
      <div class="wcp-form-input">
        <input type="time" class="wcp-form-control" id="wcp-time" name="time" placeholder="<?php esc_attr_e( 'hh:mm', 'woocommerce-confirm-payment' ) ?>">
      </div>
    </div>

    <div class="wcp-form-group">
      <label class="wcp-form-label" for="wcp-slip"><?php esc_html_e( 'Transfer slip', 'woocommerce-confirm-payment' ) ?><span class="required">*</span></label>
      <div class="wcp-form-input">
        <input type="file" id="wcp-slip" name="slip" class="wcp-form-control" accept="image/png, image/jpeg, image/gif, image/gif, application/pdf">
      </div>
    </div>

    <div class="wcp-form-group">
      <input type="hidden" name="action" value="wcp_confirm_payment">
      <?php wp_nonce_field( 'wcp_form_nonce_action', 'wcp_form_security_nonce' ); ?>
      <div class="wcp-form-response"></div>
      <button type="submit" class="button wcp-button-confirm"><span><?php esc_html_e( 'Submit', 'woocommerce-confirm-payment' ) ?></span></button>
    </div>

  </div>

  <?php do_action( 'woocommerce_confirm_payment_form_end' ) ?>

</form>