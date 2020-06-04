<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://javmah.tk
 * @since      1.0.0
 *
 * @package    Wootrello
 * @subpackage Wootrello/admin
*/
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wootrello
 * @subpackage Wootrello/admin
 * @author     javmah <jaedmah@gmail.com>
*/
class Wootrello_Settings
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $active_plugins = array() ;
    /**
     * WooCommerce Order statuses .
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $key    trello Application key of this plugin.
     */
    private  $order_statuses = array() ;
    /**
     *  trello Application key of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $key    trello Application key of this plugin.
     */
    private  $key = '7385fea630899510fd036b6e89b90c60' ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        # plugin name
        $this->plugin_name = $plugin_name;
        # Plugin version
        $this->version = $version;
        # Active plugins
        $this->active_plugins = get_option( 'active_plugins' );
        # order statuses || if have ; remove error if wc is not installed;
        if ( function_exists( "wc_get_order_statuses" ) ) {
            $this->order_statuses = wc_get_order_statuses();
        }
    }
    
    /**
     * Register the stylesheets for the admin area.
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/wootrello-admin.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the admin area.
     * @since    1.0.0
     */
    public function settings_enqueue_scripts( $hook )
    {
        
        if ( get_current_screen()->id == $hook ) {
            wp_enqueue_script(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'js/wootrello.js',
                array( 'jquery' ),
                $this->version,
                false
            );
            $wootrello_data = array(
                'wootrelloAjaxURL' => admin_url( 'admin-ajax.php' ),
                'security'         => wp_create_nonce( 'wootrello-ajax-nonce' ),
            );
            wp_localize_script( $this->plugin_name, 'wootrello_data', $wootrello_data );
        }
    
    }
    
    /**
     * Menu page.
     * @since    1.0.0
     */
    public function Wootrello_menu_pages( $value = '' )
    {
        add_menu_page(
            __( 'WooTrello', 'wootrello' ),
            __( 'WooTrello', 'wootrello' ),
            'manage_options',
            'wootrello',
            array( $this, 'Wootrello_settings_view' ),
            'dashicons-upload'
        );
    }
    
    /**
     * Menu view Page, URL Router , Log view function , log delete function 
     * This is one of the Most Important function; 
     * @since    2.0.0
     */
    public function Wootrello_settings_view( $value = '' )
    {
        
        if ( isset( $_GET['action'] ) && $_GET['action'] == 'log' ) {
            # For Log Page
            ?>
				<div class="wrap">
					<h1 class="wp-heading-inline"> <?php 
            _e( 'Wootrello Log Page <code>last 100 log </code>', 'wpgsi' );
            ?> </h1>
					<?php 
            $wootrello_log = get_posts( array(
                'post_type'      => 'wootrello_log',
                'order'          => 'DESC',
                'posts_per_page' => -1,
            ) );
            $i = 1;
            foreach ( $wootrello_log as $key => $log ) {
                // $post_excerpt = json_decode( $log->post_excerpt  );
                
                if ( $log->post_title == 200 ) {
                    echo  "<div class='notice notice-success inline'>" ;
                } else {
                    echo  "<div class='notice notice-error inline'>" ;
                }
                
                echo  "<p><span class='wpgsi-circle'>" . $log->ID ;
                echo  " .</span>" ;
                echo  "<code>" . $log->post_title . "</code>" ;
                echo  "<code>" ;
                if ( isset( $log->post_excerpt ) ) {
                    echo  $log->post_excerpt ;
                }
                echo  "</code>" ;
                echo  $log->post_content ;
                echo  " <code>" . $log->post_date . "</code>" ;
                echo  "</p>" ;
                echo  "</div>" ;
                $i++;
            }
            ?>
				</div>
			<?php 
        } else {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wootrello-settings-display.php';
        }
        
        # Delete log after 100; starts
        $wootrello_log = get_posts( array(
            'post_type'      => 'wootrello_log',
            'posts_per_page' => -1,
        ) );
        if ( count( $wootrello_log ) > 100 ) {
            foreach ( $wootrello_log as $key => $log ) {
                if ( $key > 100 ) {
                    wp_delete_post( $log->ID, true );
                }
            }
        }
        # Delete log after 100; ends
    }
    
    /**
     * Admin notice function;
     * @since    1.0.0
     */
    public function wootrello_settings_notice()
    {
        
        if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', $this->active_plugins ) ) ) {
            echo  "<div class='notice notice-error'>" ;
            echo  " <p> <b> <a style='text-decoration: none;' href='https://wordpress.org/plugins/woocommerce'> WooCommerce </a> </b> is not Activate, <a style='text-decoration: none;' href='https://wordpress.org/plugins/wootrello'> WooTrello </a> is for connecting  Woocommerce with Trello ! </p>" ;
            echo  "</div>" ;
            # Error log
            $this->wootrello_log( 'wootrello_settings_notice', 409, 'woocommerce is not installed ' );
        }
        
        # Testing should be here !
    }
    
    /**
     * getting Open Boards
     * @since    2.0.0
     */
    public function wootrello_trello_boards( $token = '' )
    {
        if ( empty($token) ) {
            return array( 0, "Empty trello token" );
        }
        $url = 'https://api.trello.com/1/members/me/boards?&filter=open&key=' . $this->key . '&token=' . $token . '';
        $trello_returns = wp_remote_get( $url, array() );
        $boards = array();
        
        if ( !is_wp_error( $trello_returns ) && isset( $trello_returns['response']['code'] ) && $trello_returns['response']['code'] == 200 ) {
            foreach ( json_decode( $trello_returns['body'], true ) as $key => $value ) {
                $boards[$value['id']] = $value['name'];
            }
            return array( $trello_returns['response']['code'], $boards );
        } else {
            # Error Log
            $this->wootrello_log( 'wootrello_trello_boards', 410, json_encode( $trello_returns, true ) );
            return array( 410, array() );
        }
    
    }
    
    /**
     * Getting Lists
     * @since    2.0.0
     */
    public function wootrello_board_lists( $token = '', $board_id = '' )
    {
        if ( empty($token) || empty($board_id) ) {
            return;
        }
        $url = 'https://api.trello.com/1/boards/' . $board_id . '/lists?filter=open&key=' . $this->key . '&token=' . $token . '';
        $trello_returns = wp_remote_get( $url, array() );
        $lists = array();
        
        if ( isset( $trello_returns['response']['code'] ) && $trello_returns['response']['code'] == 200 ) {
            foreach ( json_decode( $trello_returns['body'], true ) as $key => $value ) {
                $lists[$value['id']] = $value['name'];
            }
        } else {
            # Error Log
            $this->wootrello_log( 'wootrello_board_lists', 420, json_encode( $trello_returns, true ) );
        }
        
        return array( $trello_returns['response']['code'], $lists );
    }
    
    /**
     * WooCommerce Order  HOOK's callback function
     * woocommerce_order_status_changed hook callback function 
     * @since    1.0.0
     * @param     int     $order_id     Order ID
     */
    public function wootrello_woocommerce_order_status_changed( $order_id, $this_status_transition_from, $this_status_transition_to )
    {
        $order = wc_get_order( $order_id );
        $order_data = array();
        $order_data['orderID'] = ( method_exists( $order, 'get_id' ) && is_int( $order->get_id() ) ? $order->get_id() : "" );
        $order_data['cart_tax'] = ( method_exists( $order, 'get_cart_tax' ) && is_string( $order->get_cart_tax() ) ? $order->get_cart_tax() : "" );
        $order_data['currency'] = ( method_exists( $order, 'get_currency' ) && is_string( $order->get_currency() ) ? $order->get_currency() : "" );
        $order_data['discount_tax'] = ( method_exists( $order, 'get_discount_tax' ) && is_string( $order->get_discount_tax() ) ? $order->get_discount_tax() : "" );
        $order_data['discount_total'] = ( method_exists( $order, 'get_discount_total' ) && is_string( $order->get_discount_total() ) ? $order->get_discount_total() : "" );
        $order_data['fees'] = ( method_exists( $order, 'get_fees' ) && is_array( $order->get_fees() ) ? implode( ", ", $order->get_fees() ) : "" );
        $order_data['shipping_method'] = ( method_exists( $order, 'get_shipping_method' ) && is_string( $order->get_shipping_method() ) ? $order->get_shipping_method() : "" );
        $order_data['shipping_tax'] = ( method_exists( $order, 'get_shipping_tax' ) && is_string( $order->get_shipping_tax() ) ? $order->get_shipping_tax() : "" );
        $order_data['shipping_total'] = ( method_exists( $order, 'get_shipping_total' ) && is_string( $order->get_shipping_total() ) ? $order->get_shipping_total() : "" );
        $order_data['subtotal'] = ( method_exists( $order, 'get_subtotal' ) && is_float( $order->get_subtotal() ) ? $order->get_subtotal() : "" );
        $order_data['subtotal_to_display'] = ( method_exists( $order, 'get_subtotal_to_display' ) && is_string( $order->get_subtotal_to_display() ) ? $order->get_subtotal_to_display() : "" );
        $order_data['tax_totals'] = ( method_exists( $order, 'get_tax_totals' ) && is_array( $order->get_tax_totals() ) ? implode( ", ", $order->get_tax_totals() ) : "" );
        $order_data['taxes'] = ( method_exists( $order, 'get_taxes' ) && is_array( $order->get_taxes() ) ? implode( ", ", $order->get_taxes() ) : "" );
        $order_data['total'] = ( method_exists( $order, 'get_total' ) && is_string( $order->get_total() ) ? $order->get_total() : "" );
        $order_data['total_discount'] = ( method_exists( $order, 'get_total_discount' ) && is_float( $order->get_total_discount() ) ? $order->get_total_discount() : "" );
        $order_data['total_tax'] = ( method_exists( $order, 'get_total_tax' ) && is_string( $order->get_total_tax() ) ? $order->get_total_tax() : "" );
        $order_data['total_refunded'] = ( method_exists( $order, 'get_total_refunded' ) && is_float( $order->get_total_refunded() ) ? $order->get_total_refunded() : "" );
        $order_data['total_tax_refunded'] = ( method_exists( $order, 'get_total_tax_refunded' ) && is_int( $order->get_total_tax_refunded() ) ? $order->get_total_tax_refunded() : "" );
        $order_data['total_shipping_refunded'] = ( method_exists( $order, 'get_total_shipping_refunded' ) && is_int( $order->get_total_shipping_refunded() ) ? $order->get_total_shipping_refunded() : "" );
        $order_data['item_count_refunded'] = ( method_exists( $order, 'get_item_count_refunded' ) && is_int( $order->get_item_count_refunded() ) ? $order->get_item_count_refunded() : "" );
        $order_data['total_qty_refunded'] = ( method_exists( $order, 'get_total_qty_refunded' ) && is_int( $order->get_total_qty_refunded() ) ? $order->get_total_qty_refunded() : "" );
        $order_data['remaining_refund_amount'] = ( method_exists( $order, 'get_remaining_refund_amount' ) && is_string( $order->get_remaining_refund_amount() ) ? $order->get_remaining_refund_amount() : "" );
        # Order Item process Starts
        
        if ( is_array( $order->get_items() ) ) {
            $items = array();
            foreach ( $order->get_items() as $item_id => $item_data ) {
                $items[$item_id]['product_id'] = ( method_exists( $item_data, "get_product_id" ) && is_int( $item_data->get_product_id() ) ? $item_data->get_product_id() : "" );
                $items[$item_id]['product_name'] = ( method_exists( $item_data, "get_name" ) && is_string( $item_data->get_name() ) ? $item_data->get_name() : "" );
                $items[$item_id]['qty'] = ( method_exists( $item_data, "get_quantity" ) && is_int( $item_data->get_quantity() ) ? $item_data->get_quantity() : "" );
                $items[$item_id]['product_qty_price'] = ( method_exists( $item_data, "get_total" ) && is_string( $item_data->get_total() ) ? $item_data->get_total() : "" );
            }
        }
        
        # Order Item process Ends
        $order_data['items'] = ( $items ? $items : "" );
        $order_data['item_count'] = ( method_exists( $order, 'get_item_count' ) && is_int( $order->get_item_count() ) ? $order->get_item_count() : "" );
        $order_data['downloadable_items'] = ( method_exists( $order, 'get_downloadable_items' ) && is_array( $order->get_downloadable_items() ) ? implode( ", ", $order->get_downloadable_items() ) : "" );
        // Need To Change
        #
        $order_data['date_created'] = ( method_exists( $order, 'get_date_created' ) && is_object( $order->get_date_created() ) ? $order->get_date_created()->date( "F j, Y, g:i:s A T" ) : "" );
        $order_data['date_modified'] = ( method_exists( $order, 'get_date_modified' ) && is_object( $order->get_date_modified() ) ? $order->get_date_modified()->date( "F j, Y, g:i:s A T" ) : "" );
        $order_data['date_completed'] = ( method_exists( $order, 'get_date_completed' ) && is_object( $order->get_date_completed() ) ? $order->get_date_completed()->date( "F j, Y, g:i:s A T" ) : $order->get_date_completed() );
        $order_data['date_paid'] = ( method_exists( $order, 'get_date_paid' ) && is_object( $order->get_date_paid() ) ? $order->get_date_paid()->date( "F j, Y, g:i:s A T" ) : $order->get_date_paid() );
        #
        $order_data['customer_id'] = ( method_exists( $order, 'get_customer_id' ) && is_int( $order->get_customer_id() ) ? $order->get_customer_id() : "" );
        $order_data['user_id'] = ( method_exists( $order, 'get_user_id' ) && is_int( $order->get_user_id() ) ? $order->get_user_id() : "" );
        $order_data['user'] = ( method_exists( $order, 'get_user' ) && is_object( $order->get_user() ) ? $order->get_user()->user_login . " - " . $order->get_user()->user_email : "" );
        $order_data['customer_ip_address'] = ( method_exists( $order, 'get_customer_ip_address' ) && is_string( $order->get_customer_ip_address() ) ? $order->get_customer_ip_address() : "" );
        $order_data['customer_user_agent'] = ( method_exists( $order, 'get_customer_user_agent' ) && is_string( $order->get_customer_user_agent() ) ? $order->get_customer_user_agent() : "" );
        $order_data['created_via'] = ( method_exists( $order, 'get_created_via' ) && is_string( $order->get_created_via() ) ? $order->get_created_via() : "" );
        $order_data['customer_note'] = ( method_exists( $order, 'get_customer_note' ) && is_string( $order->get_customer_note() ) ? $order->get_customer_note() : "" );
        $order_data['billing_first_name'] = ( method_exists( $order, 'get_billing_first_name' ) && is_string( $order->get_billing_first_name() ) ? $order->get_billing_first_name() : "" );
        $order_data['billing_last_name'] = ( method_exists( $order, 'get_billing_last_name' ) && is_string( $order->get_billing_last_name() ) ? $order->get_billing_last_name() : "" );
        $order_data['billing_company'] = ( method_exists( $order, 'get_billing_company' ) && is_string( $order->get_billing_company() ) ? $order->get_billing_company() : "" );
        $order_data['billing_address_1'] = ( method_exists( $order, 'get_billing_address_1' ) && is_string( $order->get_billing_address_1() ) ? $order->get_billing_address_1() : "" );
        $order_data['billing_address_2'] = ( method_exists( $order, 'get_billing_address_2' ) && is_string( $order->get_billing_address_2() ) ? $order->get_billing_address_2() : "" );
        $order_data['billing_city'] = ( method_exists( $order, 'get_billing_city' ) && is_string( $order->get_billing_city() ) ? $order->get_billing_city() : "" );
        $order_data['billing_state'] = ( method_exists( $order, 'get_billing_state' ) && is_string( $order->get_billing_state() ) ? $order->get_billing_state() : "" );
        $order_data['billing_postcode'] = ( method_exists( $order, 'get_billing_postcode' ) && is_string( $order->get_billing_postcode() ) ? $order->get_billing_postcode() : "" );
        $order_data['billing_country'] = ( method_exists( $order, 'get_billing_country' ) && is_string( $order->get_billing_country() ) ? $order->get_billing_country() : "" );
        $order_data['billing_email'] = ( method_exists( $order, 'get_billing_email' ) && is_string( $order->get_billing_email() ) ? $order->get_billing_email() : "" );
        $order_data['billing_phone'] = ( method_exists( $order, 'get_billing_phone' ) && is_string( $order->get_billing_phone() ) ? $order->get_billing_phone() : "" );
        $order_data['shipping_first_name'] = ( method_exists( $order, 'get_shipping_first_name' ) && is_string( $order->get_shipping_first_name() ) ? $order->get_shipping_first_name() : "" );
        $order_data['shipping_last_name'] = ( method_exists( $order, 'get_shipping_last_name' ) && is_string( $order->get_shipping_last_name() ) ? $order->get_shipping_last_name() : "" );
        $order_data['shipping_company'] = ( method_exists( $order, 'get_shipping_company' ) && is_string( $order->get_shipping_company() ) ? $order->get_shipping_company() : "" );
        $order_data['shipping_address_1'] = ( method_exists( $order, 'get_shipping_address_1' ) && is_string( $order->get_shipping_address_1() ) ? $order->get_shipping_address_1() : "" );
        $order_data['shipping_address_2'] = ( method_exists( $order, 'get_shipping_address_2' ) && is_string( $order->get_shipping_address_2() ) ? $order->get_shipping_address_2() : "" );
        $order_data['shipping_city'] = ( method_exists( $order, 'get_shipping_city' ) && is_string( $order->get_shipping_city() ) ? $order->get_shipping_city() : "" );
        $order_data['shipping_state'] = ( method_exists( $order, 'get_shipping_state' ) && is_string( $order->get_shipping_state() ) ? $order->get_shipping_state() : "" );
        $order_data['shipping_postcode'] = ( method_exists( $order, 'get_shipping_postcode' ) && is_string( $order->get_shipping_postcode() ) ? $order->get_shipping_postcode() : "" );
        $order_data['shipping_country'] = ( method_exists( $order, 'get_shipping_country' ) && is_string( $order->get_shipping_country() ) ? $order->get_shipping_country() : "" );
        $order_data['address'] = ( method_exists( $order, 'get_address' ) && is_array( $order->get_address() ) ? implode( ", ", $order->get_address() ) : "" );
        $order_data['shipping_address_map_url'] = ( method_exists( $order, 'get_shipping_address_map_url' ) && is_string( $order->get_shipping_address_map_url() ) ? $order->get_shipping_address_map_url() : "" );
        $order_data['formatted_billing_full_name'] = ( method_exists( $order, 'get_formatted_billing_full_name' ) && is_string( $order->get_formatted_billing_full_name() ) ? $order->get_formatted_billing_full_name() : "" );
        $order_data['formatted_shipping_full_name'] = ( method_exists( $order, 'get_formatted_shipping_full_name' ) && is_string( $order->get_formatted_shipping_full_name() ) ? $order->get_formatted_shipping_full_name() : "" );
        $order_data['formatted_billing_address'] = ( method_exists( $order, 'get_formatted_billing_address' ) && is_string( $order->get_formatted_billing_address() ) ? $order->get_formatted_billing_address() : "" );
        $order_data['formatted_shipping_address'] = ( method_exists( $order, 'get_formatted_shipping_address' ) && is_string( $order->get_formatted_shipping_address() ) ? $order->get_formatted_shipping_address() : "" );
        #
        $order_data['payment_method'] = ( method_exists( $order, 'get_payment_method' ) && is_string( $order->get_payment_method() ) ? $order->get_payment_method() : "" );
        $order_data['payment_method_title'] = ( method_exists( $order, 'get_payment_method_title' ) && is_string( $order->get_payment_method_title() ) ? $order->get_payment_method_title() : "" );
        $order_data['transaction_id'] = ( method_exists( $order, 'get_transaction_id' ) && is_string( $order->get_transaction_id() ) ? $order->get_transaction_id() : "" );
        #
        $order_data['checkout_payment_url'] = ( method_exists( $order, 'get_checkout_payment_url' ) && is_string( $order->get_checkout_payment_url() ) ? $order->get_checkout_payment_url() : "" );
        $order_data['checkout_order_received_url'] = ( method_exists( $order, 'get_checkout_order_received_url' ) && is_string( $order->get_checkout_order_received_url() ) ? $order->get_checkout_order_received_url() : "" );
        $order_data['cancel_order_url'] = ( method_exists( $order, 'get_cancel_order_url' ) && is_string( $order->get_cancel_order_url() ) ? $order->get_cancel_order_url() : "" );
        $order_data['cancel_order_url_raw'] = ( method_exists( $order, 'get_cancel_order_url_raw' ) && is_string( $order->get_cancel_order_url_raw() ) ? $order->get_cancel_order_url_raw() : "" );
        $order_data['cancel_endpoint'] = ( method_exists( $order, 'get_cancel_endpoint' ) && is_string( $order->get_cancel_endpoint() ) ? $order->get_cancel_endpoint() : "" );
        $order_data['view_order_url'] = ( method_exists( $order, 'get_view_order_url' ) && is_string( $order->get_view_order_url() ) ? $order->get_view_order_url() : "" );
        $order_data['edit_order_url'] = ( method_exists( $order, 'get_edit_order_url' ) && is_string( $order->get_edit_order_url() ) ? $order->get_edit_order_url() : "" );
        #
        $order_data['status'] = $this_status_transition_to;
        if ( $order_id ) {
            $this->wootrello_create_trello_card( $this_status_transition_to, $order_data );
        }
    }
    
    /**
     * woocommerce_new_orders New Order  HOOK's callback function
     * I WILL USE THIS FOR ADMIN FRONT -> woocommerce_thankyou HOOK for FRONT END
     * @since    1.0.0
     * @param     int     $order_id     Order ID
     */
    public function wootrello_woocommerce_new_order_admin( $order_id )
    {
        $order = wc_get_order( $order_id );
        # if not admin returns
        if ( $order->get_created_via() != 'admin' ) {
            return;
        }
        $order_data = array();
        $order_data['orderID'] = ( method_exists( $order, 'get_id' ) && is_int( $order->get_id() ) ? $order->get_id() : "" );
        $order_data['cart_tax'] = ( method_exists( $order, 'get_cart_tax' ) && is_string( $order->get_cart_tax() ) ? $order->get_cart_tax() : "" );
        $order_data['currency'] = ( method_exists( $order, 'get_currency' ) && is_string( $order->get_currency() ) ? $order->get_currency() : "" );
        $order_data['discount_tax'] = ( method_exists( $order, 'get_discount_tax' ) && is_string( $order->get_discount_tax() ) ? $order->get_discount_tax() : "" );
        $order_data['discount_total'] = ( method_exists( $order, 'get_discount_total' ) && is_string( $order->get_discount_total() ) ? $order->get_discount_total() : "" );
        $order_data['fees'] = ( method_exists( $order, 'get_fees' ) && is_array( $order->get_fees() ) ? implode( ", ", $order->get_fees() ) : "" );
        $order_data['shipping_method'] = ( method_exists( $order, 'get_shipping_method' ) && is_string( $order->get_shipping_method() ) ? $order->get_shipping_method() : "" );
        $order_data['shipping_tax'] = ( method_exists( $order, 'get_shipping_tax' ) && is_string( $order->get_shipping_tax() ) ? $order->get_shipping_tax() : "" );
        $order_data['shipping_total'] = ( method_exists( $order, 'get_shipping_total' ) && is_string( $order->get_shipping_total() ) ? $order->get_shipping_total() : "" );
        $order_data['subtotal'] = ( method_exists( $order, 'get_subtotal' ) && is_float( $order->get_subtotal() ) ? $order->get_subtotal() : "" );
        $order_data['subtotal_to_display'] = ( method_exists( $order, 'get_subtotal_to_display' ) && is_string( $order->get_subtotal_to_display() ) ? $order->get_subtotal_to_display() : "" );
        $order_data['tax_totals'] = ( method_exists( $order, 'get_tax_totals' ) && is_array( $order->get_tax_totals() ) ? implode( ", ", $order->get_tax_totals() ) : "" );
        $order_data['taxes'] = ( method_exists( $order, 'get_taxes' ) && is_array( $order->get_taxes() ) ? implode( ", ", $order->get_taxes() ) : "" );
        $order_data['total'] = ( method_exists( $order, 'get_total' ) && is_string( $order->get_total() ) ? $order->get_total() : "" );
        $order_data['total_discount'] = ( method_exists( $order, 'get_total_discount' ) && is_float( $order->get_total_discount() ) ? $order->get_total_discount() : "" );
        $order_data['total_tax'] = ( method_exists( $order, 'get_total_tax' ) && is_string( $order->get_total_tax() ) ? $order->get_total_tax() : "" );
        $order_data['total_refunded'] = ( method_exists( $order, 'get_total_refunded' ) && is_float( $order->get_total_refunded() ) ? $order->get_total_refunded() : "" );
        $order_data['total_tax_refunded'] = ( method_exists( $order, 'get_total_tax_refunded' ) && is_int( $order->get_total_tax_refunded() ) ? $order->get_total_tax_refunded() : "" );
        $order_data['total_shipping_refunded'] = ( method_exists( $order, 'get_total_shipping_refunded' ) && is_int( $order->get_total_shipping_refunded() ) ? $order->get_total_shipping_refunded() : "" );
        $order_data['item_count_refunded'] = ( method_exists( $order, 'get_item_count_refunded' ) && is_int( $order->get_item_count_refunded() ) ? $order->get_item_count_refunded() : "" );
        $order_data['total_qty_refunded'] = ( method_exists( $order, 'get_total_qty_refunded' ) && is_int( $order->get_total_qty_refunded() ) ? $order->get_total_qty_refunded() : "" );
        $order_data['remaining_refund_amount'] = ( method_exists( $order, 'get_remaining_refund_amount' ) && is_string( $order->get_remaining_refund_amount() ) ? $order->get_remaining_refund_amount() : "" );
        # Order Item process Starts
        
        if ( is_array( $order->get_items() ) ) {
            $items = array();
            foreach ( $order->get_items() as $item_id => $item_data ) {
                $items[$item_id]['product_id'] = ( method_exists( $item_data, "get_product_id" ) && is_int( $item_data->get_product_id() ) ? $item_data->get_product_id() : "" );
                $items[$item_id]['product_name'] = ( method_exists( $item_data, "get_name" ) && is_string( $item_data->get_name() ) ? $item_data->get_name() : "" );
                $items[$item_id]['qty'] = ( method_exists( $item_data, "get_quantity" ) && is_int( $item_data->get_quantity() ) ? $item_data->get_quantity() : "" );
                $items[$item_id]['product_qty_price'] = ( method_exists( $item_data, "get_total" ) && is_string( $item_data->get_total() ) ? $item_data->get_total() : "" );
            }
        }
        
        # Order Item process Ends
        $order_data['items'] = ( $items ? $items : "" );
        $order_data['item_count'] = ( method_exists( $order, 'get_item_count' ) && is_int( $order->get_item_count() ) ? $order->get_item_count() : "" );
        $order_data['downloadable_items'] = ( method_exists( $order, 'get_downloadable_items' ) && is_array( $order->get_downloadable_items() ) ? implode( ", ", $order->get_downloadable_items() ) : "" );
        // Need To Change
        #
        $order_data['date_created'] = ( method_exists( $order, 'get_date_created' ) && is_object( $order->get_date_created() ) ? $order->get_date_created()->date( "F j, Y, g:i:s A T" ) : "" );
        $order_data['date_modified'] = ( method_exists( $order, 'get_date_modified' ) && is_object( $order->get_date_modified() ) ? $order->get_date_modified()->date( "F j, Y, g:i:s A T" ) : "" );
        $order_data['date_completed'] = ( method_exists( $order, 'get_date_completed' ) && is_object( $order->get_date_completed() ) ? $order->get_date_completed()->date( "F j, Y, g:i:s A T" ) : $order->get_date_completed() );
        $order_data['date_paid'] = ( method_exists( $order, 'get_date_paid' ) && is_object( $order->get_date_paid() ) ? $order->get_date_paid()->date( "F j, Y, g:i:s A T" ) : $order->get_date_paid() );
        #
        $order_data['customer_id'] = ( method_exists( $order, 'get_customer_id' ) && is_int( $order->get_customer_id() ) ? $order->get_customer_id() : "" );
        $order_data['user_id'] = ( method_exists( $order, 'get_user_id' ) && is_int( $order->get_user_id() ) ? $order->get_user_id() : "" );
        $order_data['user'] = ( method_exists( $order, 'get_user' ) && is_object( $order->get_user() ) ? $order->get_user()->user_login . " - " . $order->get_user()->user_email : "" );
        $order_data['customer_ip_address'] = ( method_exists( $order, 'get_customer_ip_address' ) && is_string( $order->get_customer_ip_address() ) ? $order->get_customer_ip_address() : "" );
        $order_data['customer_user_agent'] = ( method_exists( $order, 'get_customer_user_agent' ) && is_string( $order->get_customer_user_agent() ) ? $order->get_customer_user_agent() : "" );
        $order_data['created_via'] = ( method_exists( $order, 'get_created_via' ) && is_string( $order->get_created_via() ) ? $order->get_created_via() : "" );
        $order_data['customer_note'] = ( method_exists( $order, 'get_customer_note' ) && is_string( $order->get_customer_note() ) ? $order->get_customer_note() : "" );
        $order_data['billing_first_name'] = ( method_exists( $order, 'get_billing_first_name' ) && is_string( $order->get_billing_first_name() ) ? $order->get_billing_first_name() : "" );
        $order_data['billing_last_name'] = ( method_exists( $order, 'get_billing_last_name' ) && is_string( $order->get_billing_last_name() ) ? $order->get_billing_last_name() : "" );
        $order_data['billing_company'] = ( method_exists( $order, 'get_billing_company' ) && is_string( $order->get_billing_company() ) ? $order->get_billing_company() : "" );
        $order_data['billing_address_1'] = ( method_exists( $order, 'get_billing_address_1' ) && is_string( $order->get_billing_address_1() ) ? $order->get_billing_address_1() : "" );
        $order_data['billing_address_2'] = ( method_exists( $order, 'get_billing_address_2' ) && is_string( $order->get_billing_address_2() ) ? $order->get_billing_address_2() : "" );
        $order_data['billing_city'] = ( method_exists( $order, 'get_billing_city' ) && is_string( $order->get_billing_city() ) ? $order->get_billing_city() : "" );
        $order_data['billing_state'] = ( method_exists( $order, 'get_billing_state' ) && is_string( $order->get_billing_state() ) ? $order->get_billing_state() : "" );
        $order_data['billing_postcode'] = ( method_exists( $order, 'get_billing_postcode' ) && is_string( $order->get_billing_postcode() ) ? $order->get_billing_postcode() : "" );
        $order_data['billing_country'] = ( method_exists( $order, 'get_billing_country' ) && is_string( $order->get_billing_country() ) ? $order->get_billing_country() : "" );
        $order_data['billing_email'] = ( method_exists( $order, 'get_billing_email' ) && is_string( $order->get_billing_email() ) ? $order->get_billing_email() : "" );
        $order_data['billing_phone'] = ( method_exists( $order, 'get_billing_phone' ) && is_string( $order->get_billing_phone() ) ? $order->get_billing_phone() : "" );
        $order_data['shipping_first_name'] = ( method_exists( $order, 'get_shipping_first_name' ) && is_string( $order->get_shipping_first_name() ) ? $order->get_shipping_first_name() : "" );
        $order_data['shipping_last_name'] = ( method_exists( $order, 'get_shipping_last_name' ) && is_string( $order->get_shipping_last_name() ) ? $order->get_shipping_last_name() : "" );
        $order_data['shipping_company'] = ( method_exists( $order, 'get_shipping_company' ) && is_string( $order->get_shipping_company() ) ? $order->get_shipping_company() : "" );
        $order_data['shipping_address_1'] = ( method_exists( $order, 'get_shipping_address_1' ) && is_string( $order->get_shipping_address_1() ) ? $order->get_shipping_address_1() : "" );
        $order_data['shipping_address_2'] = ( method_exists( $order, 'get_shipping_address_2' ) && is_string( $order->get_shipping_address_2() ) ? $order->get_shipping_address_2() : "" );
        $order_data['shipping_city'] = ( method_exists( $order, 'get_shipping_city' ) && is_string( $order->get_shipping_city() ) ? $order->get_shipping_city() : "" );
        $order_data['shipping_state'] = ( method_exists( $order, 'get_shipping_state' ) && is_string( $order->get_shipping_state() ) ? $order->get_shipping_state() : "" );
        $order_data['shipping_postcode'] = ( method_exists( $order, 'get_shipping_postcode' ) && is_string( $order->get_shipping_postcode() ) ? $order->get_shipping_postcode() : "" );
        $order_data['shipping_country'] = ( method_exists( $order, 'get_shipping_country' ) && is_string( $order->get_shipping_country() ) ? $order->get_shipping_country() : "" );
        $order_data['address'] = ( method_exists( $order, 'get_address' ) && is_array( $order->get_address() ) ? implode( ", ", $order->get_address() ) : "" );
        $order_data['shipping_address_map_url'] = ( method_exists( $order, 'get_shipping_address_map_url' ) && is_string( $order->get_shipping_address_map_url() ) ? $order->get_shipping_address_map_url() : "" );
        $order_data['formatted_billing_full_name'] = ( method_exists( $order, 'get_formatted_billing_full_name' ) && is_string( $order->get_formatted_billing_full_name() ) ? $order->get_formatted_billing_full_name() : "" );
        $order_data['formatted_shipping_full_name'] = ( method_exists( $order, 'get_formatted_shipping_full_name' ) && is_string( $order->get_formatted_shipping_full_name() ) ? $order->get_formatted_shipping_full_name() : "" );
        $order_data['formatted_billing_address'] = ( method_exists( $order, 'get_formatted_billing_address' ) && is_string( $order->get_formatted_billing_address() ) ? $order->get_formatted_billing_address() : "" );
        $order_data['formatted_shipping_address'] = ( method_exists( $order, 'get_formatted_shipping_address' ) && is_string( $order->get_formatted_shipping_address() ) ? $order->get_formatted_shipping_address() : "" );
        #
        $order_data['payment_method'] = ( method_exists( $order, 'get_payment_method' ) && is_string( $order->get_payment_method() ) ? $order->get_payment_method() : "" );
        $order_data['payment_method_title'] = ( method_exists( $order, 'get_payment_method_title' ) && is_string( $order->get_payment_method_title() ) ? $order->get_payment_method_title() : "" );
        $order_data['transaction_id'] = ( method_exists( $order, 'get_transaction_id' ) && is_string( $order->get_transaction_id() ) ? $order->get_transaction_id() : "" );
        #
        $order_data['checkout_payment_url'] = ( method_exists( $order, 'get_checkout_payment_url' ) && is_string( $order->get_checkout_payment_url() ) ? $order->get_checkout_payment_url() : "" );
        $order_data['checkout_order_received_url'] = ( method_exists( $order, 'get_checkout_order_received_url' ) && is_string( $order->get_checkout_order_received_url() ) ? $order->get_checkout_order_received_url() : "" );
        $order_data['cancel_order_url'] = ( method_exists( $order, 'get_cancel_order_url' ) && is_string( $order->get_cancel_order_url() ) ? $order->get_cancel_order_url() : "" );
        $order_data['cancel_order_url_raw'] = ( method_exists( $order, 'get_cancel_order_url_raw' ) && is_string( $order->get_cancel_order_url_raw() ) ? $order->get_cancel_order_url_raw() : "" );
        $order_data['cancel_endpoint'] = ( method_exists( $order, 'get_cancel_endpoint' ) && is_string( $order->get_cancel_endpoint() ) ? $order->get_cancel_endpoint() : "" );
        $order_data['view_order_url'] = ( method_exists( $order, 'get_view_order_url' ) && is_string( $order->get_view_order_url() ) ? $order->get_view_order_url() : "" );
        $order_data['edit_order_url'] = ( method_exists( $order, 'get_edit_order_url' ) && is_string( $order->get_edit_order_url() ) ? $order->get_edit_order_url() : "" );
        #
        $order_data['status'] = $order->get_status();
        if ( $order_id ) {
            $this->wootrello_create_trello_card( $order_data['status'], $order_data );
        }
    }
    
    /**
     * woocommerce_thankyou  Order  HOOK's callback function
     * I WILL USE THIS FOR  Checkout page -> woocommerce_thankyou HOOK for FRONT END
     * @since    1.0.0
     * @param     int     $order_id     Order ID
     */
    public function wootrello_woocommerce_new_order_checkout( $order_id )
    {
        $order = wc_get_order( $order_id );
        # if not checkout returns
        if ( $order->get_created_via() != 'checkout' ) {
            return;
        }
        $order_data = array();
        $order_data['orderID'] = ( method_exists( $order, 'get_id' ) && is_int( $order->get_id() ) ? $order->get_id() : "" );
        $order_data['cart_tax'] = ( method_exists( $order, 'get_cart_tax' ) && is_string( $order->get_cart_tax() ) ? $order->get_cart_tax() : "" );
        $order_data['currency'] = ( method_exists( $order, 'get_currency' ) && is_string( $order->get_currency() ) ? $order->get_currency() : "" );
        $order_data['discount_tax'] = ( method_exists( $order, 'get_discount_tax' ) && is_string( $order->get_discount_tax() ) ? $order->get_discount_tax() : "" );
        $order_data['discount_total'] = ( method_exists( $order, 'get_discount_total' ) && is_string( $order->get_discount_total() ) ? $order->get_discount_total() : "" );
        $order_data['fees'] = ( method_exists( $order, 'get_fees' ) && is_array( $order->get_fees() ) ? implode( ", ", $order->get_fees() ) : "" );
        $order_data['shipping_method'] = ( method_exists( $order, 'get_shipping_method' ) && is_string( $order->get_shipping_method() ) ? $order->get_shipping_method() : "" );
        $order_data['shipping_tax'] = ( method_exists( $order, 'get_shipping_tax' ) && is_string( $order->get_shipping_tax() ) ? $order->get_shipping_tax() : "" );
        $order_data['shipping_total'] = ( method_exists( $order, 'get_shipping_total' ) && is_string( $order->get_shipping_total() ) ? $order->get_shipping_total() : "" );
        $order_data['subtotal'] = ( method_exists( $order, 'get_subtotal' ) && is_float( $order->get_subtotal() ) ? $order->get_subtotal() : "" );
        $order_data['subtotal_to_display'] = ( method_exists( $order, 'get_subtotal_to_display' ) && is_string( $order->get_subtotal_to_display() ) ? $order->get_subtotal_to_display() : "" );
        $order_data['tax_totals'] = ( method_exists( $order, 'get_tax_totals' ) && is_array( $order->get_tax_totals() ) ? implode( ", ", $order->get_tax_totals() ) : "" );
        $order_data['taxes'] = ( method_exists( $order, 'get_taxes' ) && is_array( $order->get_taxes() ) ? implode( ", ", $order->get_taxes() ) : "" );
        $order_data['total'] = ( method_exists( $order, 'get_total' ) && is_string( $order->get_total() ) ? $order->get_total() : "" );
        $order_data['total_discount'] = ( method_exists( $order, 'get_total_discount' ) && is_float( $order->get_total_discount() ) ? $order->get_total_discount() : "" );
        $order_data['total_tax'] = ( method_exists( $order, 'get_total_tax' ) && is_string( $order->get_total_tax() ) ? $order->get_total_tax() : "" );
        $order_data['total_refunded'] = ( method_exists( $order, 'get_total_refunded' ) && is_float( $order->get_total_refunded() ) ? $order->get_total_refunded() : "" );
        $order_data['total_tax_refunded'] = ( method_exists( $order, 'get_total_tax_refunded' ) && is_int( $order->get_total_tax_refunded() ) ? $order->get_total_tax_refunded() : "" );
        $order_data['total_shipping_refunded'] = ( method_exists( $order, 'get_total_shipping_refunded' ) && is_int( $order->get_total_shipping_refunded() ) ? $order->get_total_shipping_refunded() : "" );
        $order_data['item_count_refunded'] = ( method_exists( $order, 'get_item_count_refunded' ) && is_int( $order->get_item_count_refunded() ) ? $order->get_item_count_refunded() : "" );
        $order_data['total_qty_refunded'] = ( method_exists( $order, 'get_total_qty_refunded' ) && is_int( $order->get_total_qty_refunded() ) ? $order->get_total_qty_refunded() : "" );
        $order_data['remaining_refund_amount'] = ( method_exists( $order, 'get_remaining_refund_amount' ) && is_string( $order->get_remaining_refund_amount() ) ? $order->get_remaining_refund_amount() : "" );
        # Order Item process Starts
        
        if ( is_array( $order->get_items() ) ) {
            $items = array();
            foreach ( $order->get_items() as $item_id => $item_data ) {
                $items[$item_id]['product_id'] = ( method_exists( $item_data, "get_product_id" ) && is_int( $item_data->get_product_id() ) ? $item_data->get_product_id() : "" );
                $items[$item_id]['product_name'] = ( method_exists( $item_data, "get_name" ) && is_string( $item_data->get_name() ) ? $item_data->get_name() : "" );
                $items[$item_id]['qty'] = ( method_exists( $item_data, "get_quantity" ) && is_int( $item_data->get_quantity() ) ? $item_data->get_quantity() : "" );
                $items[$item_id]['product_qty_price'] = ( method_exists( $item_data, "get_total" ) && is_string( $item_data->get_total() ) ? $item_data->get_total() : "" );
            }
        }
        
        # Order Item process Ends
        $order_data['items'] = ( $items ? $items : "" );
        $order_data['item_count'] = ( method_exists( $order, 'get_item_count' ) && is_int( $order->get_item_count() ) ? $order->get_item_count() : "" );
        $order_data['downloadable_items'] = ( method_exists( $order, 'get_downloadable_items' ) && is_array( $order->get_downloadable_items() ) ? implode( ", ", $order->get_downloadable_items() ) : "" );
        // Need To Change
        #
        $order_data['date_created'] = ( method_exists( $order, 'get_date_created' ) && is_object( $order->get_date_created() ) ? $order->get_date_created()->date( "F j, Y, g:i:s A T" ) : "" );
        $order_data['date_modified'] = ( method_exists( $order, 'get_date_modified' ) && is_object( $order->get_date_modified() ) ? $order->get_date_modified()->date( "F j, Y, g:i:s A T" ) : "" );
        $order_data['date_completed'] = ( method_exists( $order, 'get_date_completed' ) && is_object( $order->get_date_completed() ) ? $order->get_date_completed()->date( "F j, Y, g:i:s A T" ) : $order->get_date_completed() );
        $order_data['date_paid'] = ( method_exists( $order, 'get_date_paid' ) && is_object( $order->get_date_paid() ) ? $order->get_date_paid()->date( "F j, Y, g:i:s A T" ) : $order->get_date_paid() );
        #
        $order_data['customer_id'] = ( method_exists( $order, 'get_customer_id' ) && is_int( $order->get_customer_id() ) ? $order->get_customer_id() : "" );
        $order_data['user_id'] = ( method_exists( $order, 'get_user_id' ) && is_int( $order->get_user_id() ) ? $order->get_user_id() : "" );
        $order_data['user'] = ( method_exists( $order, 'get_user' ) && is_object( $order->get_user() ) ? $order->get_user()->user_login . " - " . $order->get_user()->user_email : "" );
        $order_data['customer_ip_address'] = ( method_exists( $order, 'get_customer_ip_address' ) && is_string( $order->get_customer_ip_address() ) ? $order->get_customer_ip_address() : "" );
        $order_data['customer_user_agent'] = ( method_exists( $order, 'get_customer_user_agent' ) && is_string( $order->get_customer_user_agent() ) ? $order->get_customer_user_agent() : "" );
        $order_data['created_via'] = ( method_exists( $order, 'get_created_via' ) && is_string( $order->get_created_via() ) ? $order->get_created_via() : "" );
        $order_data['customer_note'] = ( method_exists( $order, 'get_customer_note' ) && is_string( $order->get_customer_note() ) ? $order->get_customer_note() : "" );
        $order_data['billing_first_name'] = ( method_exists( $order, 'get_billing_first_name' ) && is_string( $order->get_billing_first_name() ) ? $order->get_billing_first_name() : "" );
        $order_data['billing_last_name'] = ( method_exists( $order, 'get_billing_last_name' ) && is_string( $order->get_billing_last_name() ) ? $order->get_billing_last_name() : "" );
        $order_data['billing_company'] = ( method_exists( $order, 'get_billing_company' ) && is_string( $order->get_billing_company() ) ? $order->get_billing_company() : "" );
        $order_data['billing_address_1'] = ( method_exists( $order, 'get_billing_address_1' ) && is_string( $order->get_billing_address_1() ) ? $order->get_billing_address_1() : "" );
        $order_data['billing_address_2'] = ( method_exists( $order, 'get_billing_address_2' ) && is_string( $order->get_billing_address_2() ) ? $order->get_billing_address_2() : "" );
        $order_data['billing_city'] = ( method_exists( $order, 'get_billing_city' ) && is_string( $order->get_billing_city() ) ? $order->get_billing_city() : "" );
        $order_data['billing_state'] = ( method_exists( $order, 'get_billing_state' ) && is_string( $order->get_billing_state() ) ? $order->get_billing_state() : "" );
        $order_data['billing_postcode'] = ( method_exists( $order, 'get_billing_postcode' ) && is_string( $order->get_billing_postcode() ) ? $order->get_billing_postcode() : "" );
        $order_data['billing_country'] = ( method_exists( $order, 'get_billing_country' ) && is_string( $order->get_billing_country() ) ? $order->get_billing_country() : "" );
        $order_data['billing_email'] = ( method_exists( $order, 'get_billing_email' ) && is_string( $order->get_billing_email() ) ? $order->get_billing_email() : "" );
        $order_data['billing_phone'] = ( method_exists( $order, 'get_billing_phone' ) && is_string( $order->get_billing_phone() ) ? $order->get_billing_phone() : "" );
        $order_data['shipping_first_name'] = ( method_exists( $order, 'get_shipping_first_name' ) && is_string( $order->get_shipping_first_name() ) ? $order->get_shipping_first_name() : "" );
        $order_data['shipping_last_name'] = ( method_exists( $order, 'get_shipping_last_name' ) && is_string( $order->get_shipping_last_name() ) ? $order->get_shipping_last_name() : "" );
        $order_data['shipping_company'] = ( method_exists( $order, 'get_shipping_company' ) && is_string( $order->get_shipping_company() ) ? $order->get_shipping_company() : "" );
        $order_data['shipping_address_1'] = ( method_exists( $order, 'get_shipping_address_1' ) && is_string( $order->get_shipping_address_1() ) ? $order->get_shipping_address_1() : "" );
        $order_data['shipping_address_2'] = ( method_exists( $order, 'get_shipping_address_2' ) && is_string( $order->get_shipping_address_2() ) ? $order->get_shipping_address_2() : "" );
        $order_data['shipping_city'] = ( method_exists( $order, 'get_shipping_city' ) && is_string( $order->get_shipping_city() ) ? $order->get_shipping_city() : "" );
        $order_data['shipping_state'] = ( method_exists( $order, 'get_shipping_state' ) && is_string( $order->get_shipping_state() ) ? $order->get_shipping_state() : "" );
        $order_data['shipping_postcode'] = ( method_exists( $order, 'get_shipping_postcode' ) && is_string( $order->get_shipping_postcode() ) ? $order->get_shipping_postcode() : "" );
        $order_data['shipping_country'] = ( method_exists( $order, 'get_shipping_country' ) && is_string( $order->get_shipping_country() ) ? $order->get_shipping_country() : "" );
        $order_data['address'] = ( method_exists( $order, 'get_address' ) && is_array( $order->get_address() ) ? implode( ", ", $order->get_address() ) : "" );
        $order_data['shipping_address_map_url'] = ( method_exists( $order, 'get_shipping_address_map_url' ) && is_string( $order->get_shipping_address_map_url() ) ? $order->get_shipping_address_map_url() : "" );
        $order_data['formatted_billing_full_name'] = ( method_exists( $order, 'get_formatted_billing_full_name' ) && is_string( $order->get_formatted_billing_full_name() ) ? $order->get_formatted_billing_full_name() : "" );
        $order_data['formatted_shipping_full_name'] = ( method_exists( $order, 'get_formatted_shipping_full_name' ) && is_string( $order->get_formatted_shipping_full_name() ) ? $order->get_formatted_shipping_full_name() : "" );
        $order_data['formatted_billing_address'] = ( method_exists( $order, 'get_formatted_billing_address' ) && is_string( $order->get_formatted_billing_address() ) ? $order->get_formatted_billing_address() : "" );
        $order_data['formatted_shipping_address'] = ( method_exists( $order, 'get_formatted_shipping_address' ) && is_string( $order->get_formatted_shipping_address() ) ? $order->get_formatted_shipping_address() : "" );
        #
        $order_data['payment_method'] = ( method_exists( $order, 'get_payment_method' ) && is_string( $order->get_payment_method() ) ? $order->get_payment_method() : "" );
        $order_data['payment_method_title'] = ( method_exists( $order, 'get_payment_method_title' ) && is_string( $order->get_payment_method_title() ) ? $order->get_payment_method_title() : "" );
        $order_data['transaction_id'] = ( method_exists( $order, 'get_transaction_id' ) && is_string( $order->get_transaction_id() ) ? $order->get_transaction_id() : "" );
        #
        $order_data['checkout_payment_url'] = ( method_exists( $order, 'get_checkout_payment_url' ) && is_string( $order->get_checkout_payment_url() ) ? $order->get_checkout_payment_url() : "" );
        $order_data['checkout_order_received_url'] = ( method_exists( $order, 'get_checkout_order_received_url' ) && is_string( $order->get_checkout_order_received_url() ) ? $order->get_checkout_order_received_url() : "" );
        $order_data['cancel_order_url'] = ( method_exists( $order, 'get_cancel_order_url' ) && is_string( $order->get_cancel_order_url() ) ? $order->get_cancel_order_url() : "" );
        $order_data['cancel_order_url_raw'] = ( method_exists( $order, 'get_cancel_order_url_raw' ) && is_string( $order->get_cancel_order_url_raw() ) ? $order->get_cancel_order_url_raw() : "" );
        $order_data['cancel_endpoint'] = ( method_exists( $order, 'get_cancel_endpoint' ) && is_string( $order->get_cancel_endpoint() ) ? $order->get_cancel_endpoint() : "" );
        $order_data['view_order_url'] = ( method_exists( $order, 'get_view_order_url' ) && is_string( $order->get_view_order_url() ) ? $order->get_view_order_url() : "" );
        $order_data['edit_order_url'] = ( method_exists( $order, 'get_edit_order_url' ) && is_string( $order->get_edit_order_url() ) ? $order->get_edit_order_url() : "" );
        #
        $order_data['status'] = 'new_order';
        if ( $order_id ) {
            $this->wootrello_create_trello_card( $order_data['status'], $order_data );
        }
    }
    
    /**
     * new create trello card ;
     * @since    1.0.0
     * @param     string     $order_status     order_status
     * @param     array     $order_info        order_info
     */
    public function wootrello_create_trello_card( $order_status = "", $order_info = array() )
    {
        # Check is event is enabled for creating a trello card;
        $create_after = get_option( "wootrello_create_after" );
        
        if ( !isset( $create_after[$order_status] ) || empty($create_after[$order_status]) ) {
            $this->wootrello_log( 'wootrello_create_trello_card', 200, "Event is disabled ! Event name is :" . $order_status );
            #
            return array( FALSE, "Event is disabled !" );
        }
        
        # getting the settings options ; and checking is them is set or not
        $settings = get_option( "wootrello_trello_settings" );
        
        if ( !isset( $settings['trello_access_code'], $settings['trello_board'] ) ) {
            $this->wootrello_log( 'wootrello_create_trello_card', 450, "No trello access code or Trello board ID !" );
            #
            return array( FALSE, "No trello access code or Trello board ID !" );
        }
        
        
        if ( empty($settings['trello_access_code']) || empty($settings['trello_board']) ) {
            $this->wootrello_log( 'wootrello_create_trello_card', 460, "Empty trello access code or Trello board ID !" );
            #
            return array( FALSE, " Empty trello access code or Trello board ID !" );
        }
        
        # getting  plugin settings from options
        $card_title = get_option( "wootrello_card_title" );
        $card_description = get_option( "wootrello_card_description" );
        $product_list = get_option( "wootrello_product_list" );
        # trello card title
        $title = $order_info['orderID'];
        $title .= ( $card_title && $card_title["date"] ? ' # ' . date( "Y/m/d" ) : "" );
        $description = ' ** Order ID :** ' . urlencode( $order_info["orderID"] );
        $description .= ' %0A ** Order status :** ' . urlencode( $order_info["status"] );
        $description .= ( $card_description && $card_description["customer_name"] ? ' %0A ** Customer name :** ' . urlencode( $order_info["billing_first_name"] ) . " " . urlencode( $order_info["billing_last_name"] ) : "" );
        # billing address
        $description .= ( $card_description && $card_description["billing_address"] ? ' %0A ** Billing address :**  %0A ' . urlencode( $order_info["billing_address_1"] ) : '' );
        $description .= ( isset( $order_info["billing_address_2"] ) && !empty($order_info["billing_address_2"]) ? '  %0A ' . urlencode( $order_info["billing_address_2"] ) : '' );
        $description .= ( isset( $order_info["billing_postcode"] ) && !empty($order_info["billing_postcode"]) ? '  %0A ' . urlencode( $order_info["billing_postcode"] ) : '' );
        # shipping address
        $description .= ( $card_description && $card_description["shipping_address"] ? ' %0A ** Shipping address :**   %0A ' . urlencode( $order_info["shipping_address_1"] ) : '' );
        $description .= ( isset( $order_info["shipping_address_2"] ) && !empty($order_info["shipping_address_2"]) ? '  %0A ' . urlencode( $order_info["shipping_address_2"] ) : '' );
        $description .= ( isset( $order_info["shipping_postcode"] ) && !empty($order_info["shipping_postcode"]) ? '  %0A ' . urlencode( $order_info["shipping_postcode"] ) : '' );
        # Payment Details
        $payment_methods = ['cod' => 'наяўнымi пры дастаўцы', 'essl_webpay_gateway ' => 'карткай анлайн'];
        $description .= ( $card_description && $card_description["payment_method"] ? ' %0A ** Payment method :** ' . urlencode( $payment_methods[$order_info["payment_method"]] ) : '' );
        $description .= ( $card_description && $card_description["order_total"] ? ' %0A ** Order total  :** ' . urlencode( $order_info["total"] ) . " " . urlencode( $order_info['currency'] ) : "" );
        #3rd party orders ends  "Checkout Field Editor (Checkout Manager) for WooCommerce"
        $card_url = 'https://api.trello.com/1/cards?name=' . urlencode( $title ) . '&desc=' . $description . '&pos=top&idList=' . $settings['trello_list'] . '&keepFromSource=all&key=' . $this->key . '&token=' . $settings['trello_access_code'] . '';
        $trello_response = wp_remote_post( $card_url, array() );
        
        if ( !is_wp_error( $trello_response ) && isset( $trello_response['response']['code'] ) && $trello_response['response']['code'] == 200 ) {
            # Request is successful
            # Getting the New Created Card Id ;
            $trello_response_body = json_decode( $trello_response['body'], true );
            
            if ( isset( $trello_response_body['id'] ) && $trello_response_body['id'] ) {
                $check_list_url = 'https://api.trello.com/1/cards/' . $trello_response_body['id'] . '/checklists?name=Order Items&pos=top&key=' . $this->key . '&token=' . $settings['trello_access_code'] . '';
                # Remote request for trello check list
                $trello_checklist_response = wp_remote_post( $check_list_url, array() );
                # JSON Decode the trello check list body
                $trello_checklist_response_body = json_decode( $trello_checklist_response['body'], true );
                # Now Creating Product Check list Items ;
                
                if ( isset( $trello_checklist_response_body['id'] ) && $trello_checklist_response_body['id'] && !empty($order_info['items']) ) {
                    # Insert The Checklist Items
                    $i = 1;
                    foreach ( $order_info['items'] as $order_item ) {
                        $url = '(' . get_edit_post_link( $order_item["product_id"] ) . ')';
                        $product = "";
                        $product .= ( $product_list && $product_list["display_serial_number"] ? $i . ' - ' : "" );
                        $product .= ( $product_list && $product_list["display_product_id"] ? $order_item["product_id"] . ' - ' : "" );
                        $product .= '[' . urlencode( $order_item["product_name"] ) . '](' . get_permalink( $order_item["product_id"] ) . ')';
                        $product .= ( $product_list && $product_list["display_product_qty"] ? ' - ' . $order_item["qty"] : "" );
                        $product .= ( $product_list && $product_list["display_product_qty_price"] ? ' - ' . $order_item["product_qty_price"] : "" );
                        #
                        $trello_list_item_url = 'https://api.trello.com/1/checklists/' . $trello_checklist_response_body['id'] . '/checkItems?name=' . $product . '&pos=top&checked=false&key=' . $this->key . '&token=' . $settings['trello_access_code'] . '';
                        wp_remote_post( $trello_list_item_url, array() );
                        $i++;
                    }
                }
            
            }
        
        } else {
            $this->wootrello_log( 'wootrello_create_trello_card', 470, json_encode( $trello_response_body ) );
        }
    
    }
    
    /**
     * wootrello_user_previous_orders
     * Current order user Previous order history ;
     * @since    1.0.0
     */
    public function wootrello_user_previous_orders( $billing_email = '' )
    {
        if ( empty($billing_email) || !filter_var( $billing_email, FILTER_VALIDATE_EMAIL ) ) {
            return array( FALSE, "EMPTY Billing address or INVALID EMAIL address " );
        }
        $orders = wc_get_orders( array(
            'limit'    => -1,
            'return'   => 'objects',
            'orderby'  => 'date',
            'customer' => $billing_email,
        ) );
        if ( !count( $orders ) ) {
            return array( FALSE, "There is no orders of this user" );
        }
        $order_statuses = array();
        foreach ( $orders as $key => $value ) {
            $order_statuses[$value->get_status()][] = $value->get_id();
        }
        $status_numbers = array();
        $txt = "";
        foreach ( $order_statuses as $key => $order_ids ) {
            $status_numbers[$key] = count( $order_ids );
            $txt .= $key . " - " . count( $order_ids ) . ", ";
        }
        return array( TRUE, $status_numbers, $txt );
    }
    
    /**
     * wootrello_ajax
     * @since    1.0.0
     */
    public function wootrello_ajax()
    {
        
        if ( wp_verify_nonce( $_POST['security'], 'wootrello-ajax-nonce' ) ) {
            $boardID = sanitize_text_field( $_POST['boardID'] );
            $wootrello_trello_settings = get_option( "wootrello_trello_settings" );
            $trello_access_code = ( isset( $wootrello_trello_settings['trello_access_code'] ) && !empty($wootrello_trello_settings['trello_access_code']) ? $wootrello_trello_settings['trello_access_code'] : FALSE );
            
            if ( empty($boardID) || empty($trello_access_code) ) {
                $this->wootrello_log( 'wootrello_ajax', 472, "boardID : " . $boardID . " || access token : " . $trello_access_code . " is empty ! " );
                echo  json_encode( array( FALSE, "boardID : " . $boardID . " || access token : " . $trello_access_code . " is empty ! " ) ) ;
                exit;
            }
            
            $lists = $this->wootrello_board_lists( $trello_access_code, $boardID );
            
            if ( $lists[0] == 200 ) {
                echo  json_encode( array( TRUE, $lists[1] ), TRUE ) ;
            } else {
                $this->wootrello_log( 'wootrello_ajax', 473, json_encode( $lists ) );
                echo  json_encode( array( FALSE, "ERROR: Check the log page ! " ), TRUE ) ;
            }
        
        }
        
        exit;
    }
    
    /**
     * LOG ! For Good , This the log Method 
     * @since    1.0.0
     * @param      string    $function_name     Function name.	 [  __METHOD__  ]
     * @param      string    $status_code       The name of this plugin.
     * @param      string    $status_message    The version of this plugin.
     */
    public function wootrello_log( $function_name = '', $status_code = '', $status_message = '' )
    {
        if ( empty($status_code) || empty($status_message) ) {
            return array( FALSE, "status_code or status_message is Empty" );
        }
        $r = wp_insert_post( array(
            'post_content' => $status_message,
            'post_title'   => $status_code,
            'post_status'  => "publish",
            'post_excerpt' => $function_name,
            'post_type'    => "wootrello_log",
        ) );
        if ( $r ) {
            return array( TRUE, "Successfully inserted to the Log" );
        }
    }
    
    /**
     * date initials to Due date conversion.
     * @since    2.0.0
     * @param      string    $date initials .
     */
    public function DueDateCalc( $selected = '' )
    {
        
        if ( $selected == "1d" ) {
            $date = date( "Y-m-d", time() + 86400 );
        } elseif ( $selected == "2d" ) {
            $date = date( "Y-m-d", time() + 86400 * 2 );
        } elseif ( $selected == "3d" ) {
            $date = date( "Y-m-d", time() + 86400 * 3 );
        } elseif ( $selected == "5d" ) {
            $date = date( "Y-m-d", time() + 86400 * 5 );
        } elseif ( $selected == "1w" ) {
            $date = date( "Y-m-d", time() + 86400 * 7 );
        } elseif ( $selected == "2w" ) {
            $date = date( "Y-m-d", time() + 86400 * 14 );
        } elseif ( $selected == "1m" ) {
            $date = date( "Y-m-d", time() + 86400 * 30 );
        } elseif ( $selected == "3m" ) {
            $date = date( "Y-m-d", time() + 86400 * 90 );
        } elseif ( $selected == "6m" ) {
            $date = date( "Y-m-d", time() + 86400 * 180 );
        } else {
            $date = date( "Y-m-d", time() );
        }
        
        return $date;
    }
    
    /**
     * Third party plugin :
     * Checkout Field Editor ( Checkout Manager ) for WooCommerce
     * BETA testing;
     * @since    2.0.0
     */
    public function wootrello_woo_checkout_field_editor_pro_fields()
    {
        $active_plugins = get_option( 'active_plugins' );
        $woo_checkout_field_editor_pro = array();
        
        if ( in_array( 'woo-checkout-field-editor-pro/checkout-form-designer.php', $active_plugins ) ) {
            $a = get_option( "wc_fields_billing" );
            $b = get_option( "wc_fields_shipping" );
            $c = get_option( "wc_fields_additional" );
            if ( $a ) {
                foreach ( $a as $key => $field ) {
                    
                    if ( isset( $field['custom'] ) && $field['custom'] == 1 ) {
                        $woo_checkout_field_editor_pro[$key]['type'] = $field['type'];
                        $woo_checkout_field_editor_pro[$key]['name'] = $field['name'];
                        $woo_checkout_field_editor_pro[$key]['label'] = $field['label'];
                    }
                
                }
            }
            if ( $b ) {
                foreach ( $b as $key => $field ) {
                    
                    if ( isset( $field['custom'] ) && $field['custom'] == 1 ) {
                        $woo_checkout_field_editor_pro[$key]['type'] = $field['type'];
                        $woo_checkout_field_editor_pro[$key]['name'] = $field['name'];
                        $woo_checkout_field_editor_pro[$key]['label'] = $field['label'];
                    }
                
                }
            }
            if ( $c ) {
                foreach ( $c as $key => $field ) {
                    
                    if ( isset( $field['custom'] ) && $field['custom'] == 1 ) {
                        $woo_checkout_field_editor_pro[$key]['type'] = $field['type'];
                        $woo_checkout_field_editor_pro[$key]['name'] = $field['name'];
                        $woo_checkout_field_editor_pro[$key]['label'] = $field['label'];
                    }
                
                }
            }
        } else {
            return array( FALSE, " Checkout Field Editor aka Checkout Manager for WooCommerce is not INSTALLED." );
        }
        
        
        if ( empty($woo_checkout_field_editor_pro) ) {
            return array( FALSE, " Checkout Field Editor aka Checkout Manager for WooCommerce is EMPTY no Custom Field. " );
        } else {
            return array( TRUE, $woo_checkout_field_editor_pro );
        }
    
    }

}
// ==================   notice : this part is for programmers   ==================
// Hello, What are you doing here ? copying code or changing code or What? Looking for Trello API implementation ?
// What about the code quality?  let me know, if possible leave a 5 star review
// Looking for a JOB, do you have one ?  Thank you .
// I am from Dhaka, Bangladesh.
// What i know !
// I kow  golang, python, PHP and wordpress and javascript too , but i don't like js
// I know VUE.js , hmm
// How may you contact me! my email is in the plugin settings page.
// Beautiful Code  is changed by freemius code formatter.