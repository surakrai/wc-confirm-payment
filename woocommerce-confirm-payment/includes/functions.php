<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://www.surakrai.com/
 * @since      0.9.0
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/includes
 */


function wcp_get_confirm_payment_url( $order_id = null ){

  $options = get_option( 'wcp_option' );

  if ( empty( $options['confirm_page'] ) ) return;

  $url = get_permalink( $options['confirm_page'] );

  if ( ! empty( $order_id ) ) $url = add_query_arg( 'wcp_order_id', $order_id, $url );

  return $url;

}

function wcp_get_gateway_bacs() {

  if ( ! function_exists( 'WC' ) ) return;

  // Get all available gateways
  $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

  // Get the Bacs gateway class
  $gateway = isset( $available_gateways['bacs'] ) ? $available_gateways['bacs'] : false;

  if ( false == $gateway ) return;

  return $gateway;

}