<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.surakrai.com/
 * @since      0.9.0
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/admin
 */
class Woocommerce_Confirm_Payment_Admin {

  /**
   * The ID of this plugin.
   *
   * @since    0.9.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    0.9.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    0.9.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct( $plugin_name, $version ) {

    $this->plugin_name     = $plugin_name;
    $this->version         = $version;
    $this->options         = get_option( 'wcp_option' );

    $this->load_dependencies();

  }

  /**
   * Load the required dependencies for the Admin facing functionality.
   *
   * Include the following files that make up the plugin:
   *
   * - Woocommerce_Confirm_Payment_Admin_Setting. Registers the admin settings and page.
   * - Woocommerce_Confirm_Payment_Email
   *
   * @since    0.9.0
   * @access   private
   */
  private function load_dependencies() {

    /**
     * The class responsible for orchestrating the actions and filters of the
     * core plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-woocommerce-confirm-payment-setting.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-woocommerce-confirm-payment-email.php';

  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    0.9.0
   */
  public function enqueue_styles() {

    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Woocommerce_Confirm_Payment_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Woocommerce_Confirm_Payment_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */

    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-confirm-payment-admin.css', array(), $this->version, 'all' );

  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    0.9.0
   */
  public function enqueue_scripts() {

    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Woocommerce_Confirm_Payment_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Woocommerce_Confirm_Payment_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */
    wp_enqueue_media();
    wp_enqueue_script( 'jquery-ui-sortable' );

    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-confirm-payment-admin.js', array( 'jquery' ), $this->version, false );

    wp_localize_script( $this->plugin_name, 'WCP', array(
      'i18n' => array(
        'select'      => __( 'Select', 'woocommerce-confirm-payment' ),
        'select_logo' => __( 'Select bank logo', 'woocommerce-confirm-payment' ),
        'upload'      => __( 'Upload', 'woocommerce-confirm-payment' ),
      )
    ));

  }

  public function register_post_type() {

    $labels = array(
      'name'               => __( 'Confirm payment', 'woocommerce-confirm-payment' ),
      'menu_name'          => __( 'Confirm payment', 'woocommerce-confirm-payment' ),
      'singular_name'      => __( 'Confirm payment', 'woocommerce-confirm-payment' ),
      'search_items'       => __( 'Search Confirm payment', 'woocommerce-confirm-payment' ),
      'not_found'          => __( 'No Confirm payment found.', 'woocommerce-confirm-payment' ),
      'not_found_in_trash' => __( 'No Confirm payment found.', 'woocommerce-confirm-payment' ),
      'parent_item_colon'  => ''
    );

    $args = array(
      'labels'             => $labels,
      'public'             => false,
      'publicly_queryable' => false,
      'show_ui'            => true,
      'query_var'          => false,
      'capability_type'    => 'post',
      'capabilities'       => array(
        'create_posts'     => 'do_not_allow',
        'edit_post '       => 'do_not_allow',
        'edit_posts '      => 'do_not_allow',
      ),
      'map_meta_cap'       => true,
      'hierarchical'       => false,
      'menu_position'      => 57,
      'menu_icon'          => 'dashicons-paperclip',
      'supports'           => array( 'title' )
    );

    register_post_type( 'wcp_confirm_payment', $args );

  }

  public function flush_rewrite_rules() {

    if ( get_option('wcp_flush_rewrite_rules') ) {
      flush_rewrite_rules();
      delete_option('wcp_flush_rewrite_rules');
    }
  }

  /**
   * Register new status
   * Tutorial: http://www.sellwithwp.com/woocommerce-custom-order-status-2/
  **/
  public function register_order_status() {

    register_post_status( 'wc-checking_payment', array(
      'label'                     => __( 'Checking payment', 'woocommerce-confirm-payment' ),
      'public'                    => true,
      'exclude_from_search'       => false,
      'show_in_admin_all_list'    => true,
      'show_in_admin_status_list' => true,
      'label_count'               => _n_noop( 'Checking payment <span class="count">(%s)</span>', 'Checking payment <span class="count">(%s)</span>' )
    ) );

    register_post_status( 'wcp-pending_confirm', array(
      'label'                     => __( 'Pending confirm', 'woocommerce-confirm-payment' ),
      'public'                    => true,
      'exclude_from_search'       => false,
      'show_in_admin_all_list'    => true,
      'show_in_admin_status_list' => true,
      'label_count'               => _n_noop( 'Pending confirm <span class="count">(%s)</span>', 'Checking Payment <span class="count">(%s)</span>', 'woocommerce-confirm-payment' )
    ) );

    register_post_status( 'wcp-success', array(
      'label'                     => __( 'Success', 'woocommerce-confirm-payment' ),
      'public'                    => true,
      'exclude_from_search'       => false,
      'show_in_admin_all_list'    => true,
      'show_in_admin_status_list' => true,
      'label_count'               => _n_noop( 'Success <span class="count">(%s)</span>', 'Success <span class="count">(%s)</span>', 'woocommerce-confirm-payment' )
    ) );

    register_post_status( 'wcp-cancelled', array(
      'label'                     => __( 'Cancel', 'woocommerce-confirm-payment' ),
      'public'                    => true,
      'exclude_from_search'       => false,
      'show_in_admin_all_list'    => true,
      'show_in_admin_status_list' => true,
      'label_count'               => _n_noop( 'Cancel <span class="count">(%s)</span>', 'cancelled <span class="count">(%s)</span>', 'woocommerce-confirm-payment' )
    ) );

  }

  public function add_order_statuses( $order_statuses ) {

    $new_order_statuses = array();

    foreach ( $order_statuses as $key => $status ) {
      $new_order_statuses[ $key ] = $status;
      if ( 'wc-pending' === $key ) {
        $new_order_statuses['wc-checking_payment'] = __( 'Checking payment', 'woocommerce-confirm-payment' );
      }
    }
    return $new_order_statuses;

  }

  public function post_states( $post_states, $post ) {

    $confirm_page = isset( $this->options['confirm_page'] ) ? $this->options['confirm_page'] : '';

    if ( $confirm_page == $post->ID ) {
      $post_states[] = __( 'Confirm Payment Page', 'woocommerce-confirm-payment' );
    }

    return $post_states;

  }

  public function columns( $columns ) {

    //unset( $columns['cb'] );
    unset( $columns['date'] );
    unset( $columns['title'] );

    $columns['wcp_slip']          = __( 'Slip', 'woocommerce-confirm-payment' );
    $columns['wcp_name']          = __( 'Name', 'woocommerce-confirm-payment' );
    $columns['wcp_order']         = __( 'Order', 'woocommerce-confirm-payment' );
    $columns['wcp_bank']          = __( 'Bank', 'woocommerce-confirm-payment' );
    $columns['wcp_amount']        = __( 'Transfer amount', 'woocommerce-confirm-payment' );
    $columns['wcp_payment_date']  = __( 'Transfer date', 'woocommerce-confirm-payment' );
    $columns['wcp_phone']         = __( 'Phone', 'woocommerce-confirm-payment' );
    $columns['date']              = __( 'Date' );
    $columns['wcp_status']        = __( 'Status', 'woocommerce-confirm-payment' );
    $columns['wcp_action']        = __( 'Action', 'woocommerce-confirm-payment' );

    return $columns;

  }

  public function columns_display( $columns, $post_id ) {

    switch ( $columns ) {

      case 'wcp_slip' :
        if ( has_post_thumbnail( $post_id ) ) {
          echo '<a target="_blank" href="'. get_the_post_thumbnail_url( $post_id, 'full' ) .'">' . get_the_post_thumbnail( $post_id, 'thumbnail') . '</a>';
        }else{
          echo '<em>no slip</em>';
        }
      break;

      case 'wcp_name' :
        echo '<strong>' . get_the_title( $post_id ) . '</strong>';
        echo '<button type="button" class="toggle-row"><span class="screen-reader-text">แสดงรายละเอียดเพิ่มเติม</span></button>';
      break;

      case 'wcp_phone' :
        echo get_post_meta( $post_id, '_wcp_payment_phone', true );
      break;

      case 'wcp_payment_date' :
        $date = get_post_meta( $post_id, '_wcp_payment_date', true );
        echo date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime( $date ) );
      break;

      case 'wcp_bank' :
        echo get_post_meta( $post_id, '_wcp_payment_bank', true );
      break;

      case 'wcp_amount' :
        echo wc_price( get_post_meta( $post_id, '_wcp_payment_amount', true ) );
      break;

      case 'wcp_order' :
        $order_id = get_post_meta( $post_id, '_wcp_payment_order_id', true );
        if ( $order_id ) {
          $orders   = wc_get_order( $order_id );
          echo edit_post_link( '#' . $orders->get_order_number(), '', '', $orders->get_order_number() ) . ' (' . $orders->get_formatted_order_total() . ')';
        }
      break;

      case 'wcp_status' :

        $status = get_post_status_object( get_post_status( $post_id ) );
        printf(
          '<mark class="payment-status status-%s"><span>%s</span></mark>',
          get_post_status( $post_id ),
          $status->label
        );

      break;

      case 'wcp_action' :

        if ( 'wcp-pending_confirm' == get_post_status( $post_id ) ) {

          printf(
            '<a href="%s" class="wcp-confirm button" title="%s"><i class="dashicons dashicons-no-alt"></i></a>',
            wp_nonce_url( admin_url( 'admin-ajax.php?action=wcp_mark_payment_status&status=cancelled&payment_id=' . $post_id ), 'wcp-mark-payment-status' ),
            __( 'Cancel', 'woocommerce-confirm-payment' )
          );
          printf(
            '<a href="%s" class="wcp-confirm button" title="%s"><i class="dashicons dashicons-yes"></i></a>',
            wp_nonce_url( admin_url( 'admin-ajax.php?action=wcp_mark_payment_status&status=success&payment_id=' . $post_id ), 'wcp-mark-payment-status' ),
            __( 'Approve', 'woocommerce-confirm-payment' )
          );

        }

      break;
    }

  }

  //defining the filter that will be used so we can select posts by 'author'
  public function filter_payment_field() {

    if( 'wcp_confirm_payment' == get_current_screen()->post_type ){

      $order_id = sanitize_text_field( ( isset( $_GET['order_id'] ) ? $_GET['order_id'] : '' )  );

      $posts = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_payment_method',
        'meta_value'  => 'bacs',
        'post_type'   => 'shop_order',
        'post_status' => 'any'
      ) );

      if ( !empty( $posts ) ) {
        echo '<select name="order_id">';
        echo '<option value="">' . __( 'Select an order', 'woocommerce-confirm-payment' ) . '</option>';
        foreach ( $posts as $order ){
          $orders = wc_get_order( $order );
          echo '<option '. selected( $order_id, $orders->get_order_number() ) .' value="'. $orders->get_order_number() .'"  >#'. $orders->get_order_number() .'</option>';
        }
        echo '</select>';
      }
    }

  }

  public function filter_payment($query) {

    global $post_type, $pagenow;

    if( 'edit.php' == $pagenow && 'wcp_confirm_payment' == $post_type && $query->is_main_query() ){

      if( isset( $_GET['order_id'] ) ) {
        $order_id = absint($_GET['order_id']);
        if( $order_id ){
          $query->set( 'meta_key', '_wcp_payment_order_id' );
          $query->set( 'meta_value',  $order_id );
        }
      }

      if( isset( $_GET['payment_id'] ) ) {
        $payment_id = absint($_GET['payment_id']);
        if( $payment_id ){
          $query->set( 'post__in', array( $payment_id ) );
        }
      }

    }
  }


  public function post_date_column_status( $status ) {

    if ( is_admin() && 'wcp_confirm_payment' == get_current_screen()->post_type ) return;

    return $status;

  }

  public function remove_row_actions( $actions, $post ) {

    if ( 'wcp_confirm_payment' == $post->post_type ) { return array(); }

    return $actions;

  }

  public function remove_edit_bulk_actions($actions) {

    unset( $actions['edit'] );
    return $actions;

  }

  // define the woocommerce_admin_order_actions callback

  public function woocommerce_admin_order_actions( $actions, $the_order ) {

    if ( $the_order->has_status( array( 'checking_payment' ) ) ) {

      $actions['processing'] = array(
        'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=processing&order_id=' . $the_order->get_id() ), 'woocommerce-mark-order-status' ),
        'name'   => __( 'Processing', 'woocommerce' ),
        'action' => 'processing',
      );

      $payment_id = get_post_meta( $the_order->get_id(), '_wcp_order_payment_id', true );

      $actions['payment_detail'] = array(
        'url'    => admin_url( 'edit.php?post_type=wcp_confirm_payment&action=-1&payment_id=' . $payment_id ),
        'name'   => __( 'View payment detail', 'woocommerce-confirm-payment' ),
        'action' => 'payment_detail',
      );

    }
    return $actions;

  }

  public function approve_payment( $order_id ) {

    $payment_id = get_post_meta( $order_id, '_wcp_order_payment_id', true );

    if ( $payment_id ) {
      wp_update_post( array( 'ID' => $payment_id, 'post_status'  => 'wcp-success' ) );
    }

    $orders = wc_get_order( $order_id );

    $message = __( 'Completed payment', 'woocommerce-confirm-payment' );

    $orders->add_order_note( $message, 0, get_current_user_id() );

  }

  public function cancel_payment( $order_id ) {

    $orders = wc_get_order( $order_id );

    $message = __( 'Cancelled payment', 'woocommerce-confirm-payment' );

    $orders->add_order_note( $message, 0, get_current_user_id() );

  }

  public function mark_payment_status(){

    if ( check_admin_referer( 'wcp-mark-payment-status' ) ) {

      global $wpdb;

      $status = sanitize_text_field( $_GET['status'] );

      $payment_id  = absint( $_GET['payment_id'] );

      wp_update_post( array( 'ID' => $payment_id, 'post_status'  => 'wcp-' . $status ) );

      $order_id = get_post_meta( $payment_id, '_wcp_payment_order_id', true );

      $orders = wc_get_order( $order_id );

      if ( 'success' == $status && $orders ) {

        $orders->update_status( 'processing' );

      }else{

        $orders->update_status( 'on-hold' );

      }

    }

    wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=wcp_confirm_payment' ) );

    exit;

  }

}