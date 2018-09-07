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

  <table class="shop_table shop_table_responsive my_account_orders">
    <thead>
      <tr>
        <th class="payment-slip"><span class="nobr"></span><?php esc_html_e( 'Slip', 'woocommerce-confirm-payment' ) ?></th>
        <th class="payment-order"><span class="nobr"></span><?php esc_html_e( 'Order', 'woocommerce-confirm-payment' ) ?></th>
        <th class="payment-amount"><span class="nobr"></span><?php esc_html_e( 'Transfer amount', 'woocommerce-confirm-payment' ) ?></th>
        <th class="payment-date"><span class="nobr"></span><?php esc_html_e( 'Transfer date', 'woocommerce-confirm-payment' ) ?></th>
        <th class="payment-status"><span class="nobr"></span><?php esc_html_e( 'Status', 'woocommerce-confirm-payment' ) ?></th>
      </tr>
    </thead>

    <tbody>
      <?php if ( $payments->have_posts() ) { ?>
        <?php while ( $payments->have_posts() ) : ?>
          <?php
            $payments->the_post();
            $status   = get_post_status_object( get_post_status( get_the_ID() ) );
            $date     = get_post_meta( get_the_ID(), '_wcp_payment_date', true );
            $order_id = get_post_meta( get_the_ID(), '_wcp_payment_order_id', true );
          ?>
          <tr>
            <td class="payment-slip" data-title="<?php esc_attr_e( 'Slip', 'woocommerce-confirm-payment' ); ?>">
              <?php if ( has_post_thumbnail() ) { ?>
                <a target="_blank" href="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>">
                  <?php the_post_thumbnail( array( 50, 50 ), array( 'class' => 'wcp-slip' ) )?>
                </a>
              <?php }else{ ?>
                <?php esc_html_e( 'no slip', 'woocommerce-confirm-payment' ); ?>
              <?php } ?>
            </td>
            <td class="payment-order" data-title="<?php esc_attr_e( 'Order', 'woocommerce-confirm-payment' ); ?>">
              <a href="<?php echo wc_get_account_endpoint_url( get_option( 'woocommerce_myaccount_view_order_endpoint' ) ) . $order_id .'/' ?>">#<?php echo $order_id ?></a>
            </td>
            <td class="payment-amount" data-title="<?php esc_attr_e( 'Transfer amount', 'woocommerce-confirm-payment' ); ?>"><?php echo wc_price( get_post_meta( get_the_ID(), '_wcp_payment_amount', true ) ); ?></td>
            <td class="payment-date" data-title="<?php esc_attr_e( 'Transfer date', 'woocommerce-confirm-payment' ); ?>"><?php echo date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime( $date ) ); ?></td>
            <td class="payment-status <?php echo sanitize_html_class( $status->name ); ?>" data-title="<?php esc_attr_e( 'Status', 'woocommerce-confirm-payment' ); ?>"><?php echo esc_html( $status->label ); ?></td>
          </tr>

        <?php wp_reset_postdata(); ?>

        <?php endwhile ?>

      <?php } else { ?>
        <tr><td colspan="5"><?php esc_html_e( 'No payment found.', 'woocommerce-confirm-payment' ); ?></td></tr>
      <?php } ?>
    </tbody>
  </table>

  <?php if ( 1 < $payments->max_num_pages ) : ?>
    <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
      <?php if ( 1 !== $current_page ) : ?>
        <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'payment-history', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
      <?php endif; ?>

      <?php if ( intval( $payments->max_num_pages ) !== $current_page ) : ?>
        <a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'payment-history', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
      <?php endif; ?>
    </div>
  <?php endif; ?>