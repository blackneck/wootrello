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
        $description .= ( $card_description && $card_description["transaction_id"] ? ' %0A ** Transaction Id :** ' . urlencode( $order_info["transaction_id"] ) : '' );
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