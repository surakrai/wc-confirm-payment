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
 * @subpackage Woocommerce_Confirm_Payment/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.9.0
 * @package    Woocommerce_Confirm_Payment
 * @subpackage Woocommerce_Confirm_Payment/includes
 * @author     Surakrai  <surakraisam@gmail.com>
 */
class Woocommerce_Confirm_Payment {

  /**
   * The loader that's responsible for maintaining and registering all hooks that power
   * the plugin.
   *
   * @since    0.9.0
   * @access   protected
   * @var      Woocommerce_Confirm_Payment_Loader    $loader    Maintains and registers all hooks for the plugin.
   */
  protected $loader;

  /**
   * The unique identifier of this plugin.
   *
   * @since    0.9.0
   * @access   protected
   * @var      string    $plugin_name    The string used to uniquely identify this plugin.
   */
  protected $plugin_name;

  /**
   * The current version of the plugin.
   *
   * @since    0.9.0
   * @access   protected
   * @var      string    $version    The current version of the plugin.
   */
  protected $version;

  /**
   * Define the core functionality of the plugin.
   *
   * Set the plugin name and the plugin version that can be used throughout the plugin.
   * Load the dependencies, define the locale, and set the hooks for the admin area and
   * the public-facing side of the site.
   *
   * @since    0.9.0
   */
  public function __construct() {
    if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
      $this->version = PLUGIN_NAME_VERSION;
    } else {
      $this->version = '1.0.0';
    }
    $this->plugin_name = 'woocommerce-confirm-payment';

    $this->load_dependencies();
    $this->set_locale();
    $this->define_admin_hooks();
    $this->define_public_hooks();

  }

  /**
   * Load the required dependencies for this plugin.
   *
   * Include the following files that make up the plugin:
   *
   * - Woocommerce_Confirm_Payment_Loader. Orchestrates the hooks of the plugin.
   * - Woocommerce_Confirm_Payment_i18n. Defines internationalization functionality.
   * - Woocommerce_Confirm_Payment_Admin. Defines all hooks for the admin area.
   * - Woocommerce_Confirm_Payment_Public. Defines all hooks for the public side of the site.
   *
   * Create an instance of the loader which will be used to register the hooks
   * with WordPress.
   *
   * @since    0.9.0
   * @access   private
   */
  private function load_dependencies() {

    /**
     * The class responsible for orchestrating the actions and filters of the
     * core plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-confirm-payment-loader.php';

    /**
     * The class responsible for defining internationalization functionality
     * of the plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-confirm-payment-i18n.php';

    /**
     * The class responsible for defining all actions that occur in the admin area.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-confirm-payment-admin.php';

    /**
     * The class responsible for defining all actions that occur in the public-facing
     * side of the site.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-confirm-payment-public.php';

    $this->loader = new Woocommerce_Confirm_Payment_Loader();

  }

  /**
   * Define the locale for this plugin for internationalization.
   *
   * Uses the Woocommerce_Confirm_Payment_i18n class in order to set the domain and to register the hook
   * with WordPress.
   *
   * @since    0.9.0
   * @access   private
   */
  private function set_locale() {

    $plugin_i18n = new Woocommerce_Confirm_Payment_i18n();

    $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

  }

  /**
   * Register all of the hooks related to the admin area functionality
   * of the plugin.
   *
   * @since    0.9.0
   * @access   private
   */
  private function define_admin_hooks() {

    $plugin_admin    = new Woocommerce_Confirm_Payment_Admin( $this->get_plugin_name(), $this->get_version() );
    $plugin_settings = new Woocommerce_Confirm_Payment_Admin_Setting( $this->get_plugin_name(), $this->get_version() );
    $email           = new Woocommerce_Confirm_Payment_Email( $this->get_plugin_name(), $this->get_version() );

    $this->loader->add_action( 'admin_menu', $plugin_settings, 'add_plugin_page' );
    $this->loader->add_action( 'admin_init', $plugin_settings, 'page_init' );
    $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
    $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
    $this->loader->add_action( 'init', $plugin_admin, 'register_post_type' );
    $this->loader->add_filter( 'manage_wcp_confirm_payment_posts_columns', $plugin_admin, 'columns' );
    $this->loader->add_action( 'manage_wcp_confirm_payment_posts_custom_column', $plugin_admin, 'columns_display', 10, 2 );
    $this->loader->add_action( 'init', $plugin_admin, 'flush_rewrite_rules' );
    $this->loader->add_action( 'display_post_states', $plugin_admin, 'post_states', 10, 2 );
    $this->loader->add_action( 'init', $plugin_admin, 'register_order_status' );
    $this->loader->add_filter( 'wc_order_statuses', $plugin_admin, 'add_order_statuses' );
    $this->loader->add_filter( 'post_date_column_status', $plugin_admin, 'post_date_column_status' );
    $this->loader->add_action( 'wp_ajax_wcp_mark_payment_status', $plugin_admin, 'mark_payment_status' );
    $this->loader->add_action( 'admin_head', $plugin_admin, 'menu_payment_count' );
    $this->loader->add_filter( 'wc_order_statuses', $plugin_admin, 'add_order_statuses' );
    $this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'filter_payment_field' );
    $this->loader->add_action( 'pre_get_posts', $plugin_admin, 'filter_payment' );
    $this->loader->add_filter( 'post_row_actions', $plugin_admin, 'remove_row_actions', 10, 2  );
    $this->loader->add_filter( 'bulk_actions-edit-wcp_confirm_payment', $plugin_admin, 'remove_edit_bulk_actions', 10, 2  );
    $this->loader->add_action( 'woocommerce_order_status_checking_payment_to_processing', $plugin_admin, 'approve_payment' );
    $this->loader->add_action( 'woocommerce_order_status_checking_payment_to_on-hold', $plugin_admin, 'cancel_payment' );
    $this->loader->add_filter( 'woocommerce_admin_order_actions', $plugin_admin, 'woocommerce_admin_order_actions', 10, 2 );
    $this->loader->add_filter( 'woocommerce_email_classes', $email, 'register_email', 90, 1 );
    $this->loader->add_action( 'woocommerce_email_actions', $email, 'add_woocommerce_email_actions' );
    $this->loader->add_action( 'wcp_email_payment_details', $email, 'payment_details', 10, 4 );

  }

  /**
   * Register all of the hooks related to the public-facing functionality
   * of the plugin.
   *
   * @since    0.9.0
   * @access   private
   */
  private function define_public_hooks() {

    $plugin_public = new Woocommerce_Confirm_Payment_Public( $this->get_plugin_name(), $this->get_version() );
    $payment_history = new Woocommerce_Confirm_Payment_History( $this->get_plugin_name(), $this->get_version() );

    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
    $this->loader->add_action( 'init', $plugin_public, 'add_shortcode' );

    $this->loader->add_action( 'woocommerce_email_before_order_table', $plugin_public, 'email_instructions', 10, 3 );
    $this->loader->add_action( 'init', $plugin_public, 'remove_bank_original', 100 );
    $this->loader->add_action( 'woocommerce_thankyou_bacs', $plugin_public, 'thankyou_page' );
    $this->loader->add_action( 'woocommerce_gateway_description', $plugin_public, 'gateway_description', 10, 2 );
    $this->loader->add_action( 'template_redirect', $plugin_public, 'redirect_single_attachment' );
    $this->loader->add_action( 'wp_ajax_wcp_confirm_payment', $plugin_public, 'confirm_payment' );
    $this->loader->add_action( 'wp_ajax_nopriv_wcp_confirm_payment', $plugin_public, 'confirm_payment' );
    $this->loader->add_action( 'wp_ajax_wcp_check_order', $plugin_public, 'ajax_check_order' );
    $this->loader->add_action( 'wp_ajax_nopriv_wcp_check_order', $plugin_public, 'ajax_check_order' );
    $this->loader->add_filter( 'woocommerce_my_account_my_orders_actions', $plugin_public, 'woocommerce_my_account_my_orders_actions', 10, 2 );
    $this->loader->add_filter( 'query_vars', $plugin_public, 'add_query_vars' );

    $this->loader->add_filter( 'woocommerce_get_query_vars', $payment_history, 'woocommerce_get_query_vars' );
    $this->loader->add_filter( 'woocommerce_account_menu_items', $payment_history, 'woocommerce_menu_item', 40 );
    $this->loader->add_action( 'init', $payment_history, 'woocommerce_add_endpoints' );
    $this->loader->add_action( 'woocommerce_account_' . $this->plugin_name . '_endpoint', $payment_history, 'woocommerce_account_payment_history' );
    $this->loader->add_filter( 'woocommerce_endpoint_' . $this->plugin_name . '_title', $payment_history, 'woocommerce_endpoints_title', $this->plugin_name );

  }

  /**
   * Run the loader to execute all of the hooks with WordPress.
   *
   * @since    0.9.0
   */
  public function run() {
    $this->loader->run();
  }

  /**
   * The name of the plugin used to uniquely identify it within the context of
   * WordPress and to define internationalization functionality.
   *
   * @since     1.0.0
   * @return    string    The name of the plugin.
   */
  public function get_plugin_name() {
    return $this->plugin_name;
  }

  /**
   * The reference to the class that orchestrates the hooks with the plugin.
   *
   * @since     1.0.0
   * @return    Woocommerce_Confirm_Payment_Loader    Orchestrates the hooks of the plugin.
   */
  public function get_loader() {
    return $this->loader;
  }

  /**
   * Retrieve the version number of the plugin.
   *
   * @since     1.0.0
   * @return    string    The version number of the plugin.
   */
  public function get_version() {
    return $this->version;
  }

}
