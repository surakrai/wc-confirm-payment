<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.surakrai.com/
 * @since      0.9.0
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wcp_input_table_wrapper">
  <table class="widefat wcp_input_table" cellspacing="0">
    <thead>
      <tr>
        <th class="sort">&nbsp;</th>
        <th style="width: 45px;"><?php esc_html_e( 'logo', 'woocommerce-confirm-payment' ); ?></th>
        <th><?php esc_html_e( 'Bank name', 'woocommerce-confirm-payment' ); ?></th>
        <th><?php esc_html_e( 'Account number', 'woocommerce-confirm-payment' ); ?></th>
        <th><?php esc_html_e( 'Account name', 'woocommerce-confirm-payment' ); ?></th>
      </tr>
    </thead>
    <tbody class="accounts">
      <?php if ( !empty( $this->bank_accounts ) ) :
        foreach ( $this->bank_accounts as $key => $account ) :

          $logo = wp_get_attachment_image( $account['bank_logo'], 'thumbnail' ); ?>

          <tr class="account-item">
            <td class="sort"><i class="dashicons dashicons-menu"></i></td>
            <td>
              <div class="wcp-upload-image-wrapper<?php if( $logo ) echo ' has-logo'; ?>">
                <input type="hidden" class="widefat bank_logo upload-image" name="bank_logo[<?php echo $key ?>]" value="<?php echo $account['bank_logo']; ?>">
                <div class="wcp-image-preview"><?php echo  $logo ?></div>
                <a href="#" class="wcp-button-upload-image"><?php _e( 'Uplpad', 'woocommerce-confirm-payment' ); ?></a>
                <a href="#" class="wcp-button-remove-image">&times;</a>
              </div>
            </td>
            <td>
              <input type="text" class="widefat bank_name" name="bank_name[<?php echo $key ?>]" value="<?php echo $account['bank_name']; ?>">
            </td>
            <td>
              <input type="text" class="widefat account_number" name="account_number[<?php echo $key ?>]" value="<?php echo $account['account_number']; ?>">
            </td>
            <td>
              <input type="text" class="widefat account_name" name="account_name[<?php echo $key ?>]" value="<?php echo $account['account_name']; ?>">
            </td>
          </tr>
        <?php endforeach;
      endif ?>
    </tbody>
    <tfoot>
      <tr>
        <th class="sort">&nbsp;</th>
        <th colspan="4">
          <a href="#" class="wcp-add_rows button"><?php esc_html_e( '+ Add account', 'woocommerce-confirm-payment' ); ?></a>
          <a href="#" class="wcp-remove_rows button"><?php esc_html_e( 'Remove selected account(s)', 'woocommerce-confirm-payment' ); ?></a>
        </th>
      </tr>
    </tfoot>
  </table>
</div>