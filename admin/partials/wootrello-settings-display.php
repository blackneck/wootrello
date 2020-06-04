<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>
	<h1><?php 
esc_attr_e( 'Wootrello settings page.', 'WpAdminStyle' );
?></h1>
    <!-- Debug Code Starts -->
    <?php 

if ( isset( $_POST['wootrello_nonce'] ) && wp_verify_nonce( $_POST['wootrello_nonce'], 'wootrello_settings_nonce_value' ) ) {
    $trello_settings = array();
    $trello_settings['trello_access_code'] = ( isset( $_POST['trello_access_code'] ) && !empty($_POST['trello_access_code']) ? sanitize_text_field( trim( $_POST['trello_access_code'] ) ) : FALSE );
    $trello_settings['trello_board'] = ( isset( $_POST['trello_board'] ) && !empty($_POST['trello_board']) ? sanitize_text_field( $_POST['trello_board'] ) : FALSE );
    $trello_settings['trello_list'] = ( isset( $_POST['trello_list'] ) && !empty($_POST['trello_list']) ? sanitize_text_field( $_POST['trello_list'] ) : FALSE );
    $trello_settings['trello_label_colour'] = ( isset( $_POST['trello_label_colour'] ) && !empty($_POST['trello_label_colour']) ? sanitize_text_field( $_POST['trello_label_colour'] ) : FALSE );
    $trello_settings['trello_due_date'] = ( isset( $_POST['trello_due_date'] ) && !empty($_POST['trello_due_date']) ? sanitize_text_field( $_POST['trello_due_date'] ) : FALSE );
    $trello_settings['trello_previous_orders'] = ( isset( $_POST['trello_previous_orders'] ) && !empty($_POST['trello_previous_orders']) ? sanitize_text_field( $_POST['trello_previous_orders'] ) : FALSE );
    $card_title = array();
    $card_title['date'] = ( isset( $_POST['card_title']['date'] ) && !empty($_POST['card_title']['date']) ? TRUE : FALSE );
    $card_title['customer_name'] = ( isset( $_POST['card_title']['customer_name'] ) && !empty($_POST['card_title']['customer_name']) ? TRUE : FALSE );
    $card_title['order_total'] = ( isset( $_POST['card_title']['order_total'] ) && !empty($_POST['card_title']['order_total']) ? TRUE : FALSE );
    $card_description = array();
    $card_description['order_url'] = ( isset( $_POST['card_description']['order_url'] ) && !empty($_POST['card_description']['order_url']) ? TRUE : FALSE );
    $card_description['customer_name'] = ( isset( $_POST['card_description']['customer_name'] ) && !empty($_POST['card_description']['customer_name']) ? TRUE : FALSE );
    $card_description['customer_note'] = ( isset( $_POST['card_description']['customer_note'] ) && !empty($_POST['card_description']['customer_note']) ? TRUE : FALSE );
    $card_description['billing_address'] = ( isset( $_POST['card_description']['billing_address'] ) && !empty($_POST['card_description']['billing_address']) ? TRUE : FALSE );
    $card_description['billing_email'] = ( isset( $_POST['card_description']['billing_email'] ) && !empty($_POST['card_description']['billing_email']) ? TRUE : FALSE );
    $card_description['billing_phone'] = ( isset( $_POST['card_description']['billing_phone'] ) && !empty($_POST['card_description']['billing_phone']) ? TRUE : FALSE );
    $card_description['shipping_address'] = ( isset( $_POST['card_description']['shipping_address'] ) && !empty($_POST['card_description']['shipping_address']) ? TRUE : FALSE );
    $card_description['payment_method'] = ( isset( $_POST['card_description']['payment_method'] ) && !empty($_POST['card_description']['payment_method']) ? TRUE : FALSE );
    $card_description['shipping_method'] = ( isset( $_POST['card_description']['shipping_method'] ) && !empty($_POST['card_description']['shipping_method']) ? TRUE : FALSE );
    $card_description['shipping_total'] = ( isset( $_POST['card_description']['shipping_total'] ) && !empty($_POST['card_description']['shipping_total']) ? TRUE : FALSE );
    $card_description['discount_total'] = ( isset( $_POST['card_description']['discount_total'] ) && !empty($_POST['card_description']['discount_total']) ? TRUE : FALSE );
    $card_description['order_total'] = ( isset( $_POST['card_description']['order_total'] ) && !empty($_POST['card_description']['order_total']) ? TRUE : FALSE );
    $card_description['previous_orders'] = ( isset( $_POST['card_description']['previous_orders'] ) && !empty($_POST['card_description']['previous_orders']) ? TRUE : FALSE );
    $product_list = array();
    $product_list['display_serial_number'] = ( isset( $_POST['product_list']['display_serial_number'] ) && !empty($_POST['product_list']['display_serial_number']) ? TRUE : FALSE );
    $product_list['display_product_id'] = ( isset( $_POST['product_list']['display_product_id'] ) && !empty($_POST['product_list']['display_product_id']) ? TRUE : FALSE );
    $product_list['display_product_qty'] = ( isset( $_POST['product_list']['display_product_qty'] ) && !empty($_POST['product_list']['display_product_qty']) ? TRUE : FALSE );
    $product_list['display_product_qty_price'] = ( isset( $_POST['product_list']['display_product_qty_price'] ) && !empty($_POST['product_list']['display_product_qty_price']) ? TRUE : FALSE );
    $create_after = array();
    $create_after['new_order'] = ( isset( $_POST['create_after']['new_order'] ) && !empty($_POST['create_after']['new_order']) ? TRUE : FALSE );
    foreach ( $this->order_statuses as $status_key => $status_name ) {
        $status = '';
        
        if ( substr( $status_key, 0, 3 ) == "wc-" ) {
            $status = substr( $status_key, strpos( $status_key, "_" ) + 3 );
        } else {
            $status = $status_key;
        }
        
        $create_after[$status] = ( isset( $_POST['create_after'][$status] ) && !empty($_POST['create_after'][$status]) ? TRUE : FALSE );
    }
    # 3rd party plugin starts
    if ( in_array( 'woo-checkout-field-editor-pro/checkout-form-designer.php', $this->active_plugins ) ) {
        
        if ( isset( $_POST['woo_checkout_field_editor'] ) && is_array( $_POST['woo_checkout_field_editor'] ) ) {
            $woo_checkout_field_editor_holder = array();
            foreach ( $_POST['woo_checkout_field_editor'] as $key => $value ) {
                $woo_checkout_field_editor_holder[$key] = ( empty($value) ? FALSE : TRUE );
            }
            update_option( 'wootrello_woo_checkout_field_editor', $woo_checkout_field_editor_holder );
        }
    
    }
    # 3rd party plugin ends
    # Saving Data to the options
    update_option( 'wootrello_trello_settings', $trello_settings );
    update_option( 'wootrello_card_title', $card_title );
    update_option( 'wootrello_card_description', $card_description );
    update_option( 'wootrello_product_list', $product_list );
    update_option( 'wootrello_create_after', $create_after );
    # get option Data from DB
    # unset aka clearing some memory;
    unset( $_POST );
    unset( $trello_settings );
    unset( $card_title );
    unset( $card_description );
    unset( $product_list );
    unset( $create_after );
}

# getting data from database;
$wootrello_trello_settings = get_option( "wootrello_trello_settings" );
$wootrello_card_title = get_option( "wootrello_card_title" );
$wootrello_card_description = get_option( "wootrello_card_description" );
$wootrello_product_list = get_option( "wootrello_product_list" );
$wootrello_create_after = get_option( "wootrello_create_after" );
# 3rd party
$woo_checkout_field_editor = get_option( "wootrello_woo_checkout_field_editor" );
$wootrello_order_delivery_date_for_woocommerce_val = get_option( 'wootrello_order_delivery_date_for_woocommerce' );
# setting data from database options to array
$woo_checkout_field_editor_fields = $this->wootrello_woo_checkout_field_editor_pro_fields();
$trello_access_code = ( isset( $wootrello_trello_settings['trello_access_code'] ) && !empty($wootrello_trello_settings['trello_access_code']) ? $wootrello_trello_settings['trello_access_code'] : FALSE );
$trello_boards = $this->wootrello_trello_boards( $trello_access_code );
?>
    <!-- Debug Code Ends -->
	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">

						<button type="button" class="handlediv" aria-expanded="true" >
							<span class="screen-reader-text">Toggle panel</span>
							<!-- <span class="toggle-indicator" aria-hidden="true"></span> -->
						</button>
						<!-- Toggle -->

						<h2 class="hndle"><span><?php 
esc_attr_e( 'Configure settings', 'WpAdminStyle' );
?></span></h2>

						<div class="inside">
							<!-- Table Starts from Here  -->

                                <!-- Forms starts -->
                                <form name="wootrello_settings" method="POST" action="" >
                                <!-- Nonce fields  -->
                                <?php 
wp_nonce_field( 'wootrello_settings_nonce_value', 'wootrello_nonce' );
?>

                                <table class="widefat">
                                    <tbody>

                                        <tr class="alternate">
                                            <td class="row-title"><label for="tablecell"><?php 
esc_attr_e( 'Trello Board', 'WpAdminStyle' );
?></label></td>
                                            <td>
                                                <select name="trello_board" style="min-width:215px;" id="trello_board">
                                                    <option value="">Select trello board</option>
													<?php 
if ( $trello_boards[0] == 200 and !empty($trello_boards[0]) ) {
    foreach ( $trello_boards[1] as $key => $value ) {
        echo  "<option value='" . $key . "'" . selected( $wootrello_trello_settings['trello_board'], $key, TRUE ) . "> " . esc_html( $value ) . "</option>" ;
    }
}
?>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="row-title"><label for="tablecell"><?php 
esc_attr_e( 'Trello List', 'WpAdminStyle' );
?></label></td>
                                            <td>
                                                <select name="trello_list" style="min-width:215px;" id="trello_list">
                                                    <option value="">Select trello list</option>
													<?php 

if ( isset( $wootrello_trello_settings['trello_access_code'] ) && isset( $wootrello_trello_settings['trello_board'] ) && (!empty($wootrello_trello_settings['trello_access_code']) && !empty($wootrello_trello_settings['trello_board'])) ) {
    # getting list
    $trello_list = $this->wootrello_board_lists( $wootrello_trello_settings['trello_access_code'], $wootrello_trello_settings['trello_board'] );
    foreach ( $trello_list[1] as $key => $value ) {
        echo  "<option value='" . $key . "'" . selected( $wootrello_trello_settings['trello_list'], $key, TRUE ) . "> " . $value . "</option>" ;
    }
}

?>
                                                </select>
                                            </td>
                                        </tr>
                                        
                                        <!-- freemius starts -->
                                        <?php 
?>
                                        <!-- freemius ends -->

                                    </tbody>
                                </table>
                                            
                                <!-- freemius starts -->
                                <?php 
?>
                                <!-- freemius ends -->

                                <br class="clear" />

                                <table class="widefat">
                                    <tbody>

                                        <tr class="alternate">
                                            <td class="row-title"><label for="tablecell"><?php 
esc_attr_e( 'Title of the card ', 'WpAdminStyle' );
?></label></td>
                                            <td>

                                                <fieldset>
                                                    <legend class="screen-reader-text"> <span> Date </span> </legend>
                                                    <label for="users_can_register">
                                                        <input name="card_title[date]" <?php 
( isset( $wootrello_card_title['date'] ) ? checked( $wootrello_card_title['date'] ) : '' );
?> type="checkbox" id="title_date" value="1" />
                                                        <span><?php 
esc_attr_e( 'Date', 'WpAdminStyle' );
?></span>
                                                    </label>
                                                </fieldset>

                                                <!-- freemius starts -->
                                                <?php 
?>
                                                <!-- freemius ends -->

                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="row-title"><label for="tablecell"><?php 
esc_attr_e( 'Description of the card ', 'WpAdminStyle' );
?></label></td>
                                            <td>
                                                
                                                <!-- freemius starts -->
                                                <?php 
?>
                                                <!-- freemius ends -->

                                                <fieldset>
                                                    <legend class="screen-reader-text"> <span> Customer name </span> </legend>
                                                    <label for="users_can_register">
                                                        <input name="card_description[customer_name]" <?php 
( isset( $wootrello_card_description['customer_name'] ) ? checked( $wootrello_card_description['customer_name'] ) : '' );
?> type="checkbox" id="customer_name" value="1" />
                                                        <span><?php 
esc_attr_e( 'Customer name', 'WpAdminStyle' );
?></span>
                                                    </label>
                                                </fieldset>

                                                <!-- freemius starts -->
                                                <?php 
?>
                                                <!-- freemius ends -->

                                                <fieldset>
                                                    <legend class="screen-reader-text"> <span> Billing Address </span> </legend>
                                                    <label for="users_can_register">
                                                        <input name="card_description[billing_address]" <?php 
( isset( $wootrello_card_description['billing_address'] ) ? checked( $wootrello_card_description['billing_address'] ) : '' );
?> type="checkbox" id="billing_address" value="1" />
                                                        <span><?php 
esc_attr_e( 'Billing Address', 'WpAdminStyle' );
?></span>
                                                    </label>
                                                </fieldset>
                                                <!--  -->

                                                <fieldset>
                                                    <legend class="screen-reader-text"> <span> Shipping Address </span> </legend>
                                                    <label for="users_can_register">
                                                        <input name="card_description[shipping_address]" <?php 
( isset( $wootrello_card_description['shipping_address'] ) ? checked( $wootrello_card_description['shipping_address'] ) : '' );
?> type="checkbox" id="shipping_address" value="1" />
                                                        <span><?php 
esc_attr_e( 'Shipping Address', 'WpAdminStyle' );
?></span>
                                                    </label>
                                                </fieldset>
                                                
                                                <!-- freemius starts -->
                                                <?php 
?>
                                                <!-- freemius ends -->

                                                <fieldset>
                                                    <legend class="screen-reader-text"> <span> Payment method </span> </legend>
                                                    <label for="users_can_register">
                                                        <input name="card_description[payment_method]" <?php 
( isset( $wootrello_card_description['payment_method'] ) ? checked( $wootrello_card_description['payment_method'] ) : '' );
?> type="checkbox" id="payment_method" value="1" />
                                                        <span><?php 
esc_attr_e( 'Payment method', 'WpAdminStyle' );
?></span>
                                                    </label>
                                                </fieldset>

                                                <fieldset>
                                                    <legend class="screen-reader-text"> <span> Order total </span> </legend>
                                                    <label for="users_can_register">
                                                        <input name="card_description[order_total]" <?php 
( isset( $wootrello_card_description['order_total'] ) ? checked( $wootrello_card_description['order_total'] ) : '' );
?> type="checkbox" id="order_total" value="1" />
                                                        <span><?php 
esc_attr_e( 'Order total', 'WpAdminStyle' );
?></span>
                                                    </label>
                                                </fieldset>

                                                <!-- freemius starts -->
                                                <?php 
?>
                                                <!-- freemius ends -->

                                            </td>
                                        </tr>

                                        <tr class="alternate">
                                            <td class="row-title"><?php 
esc_attr_e( 'Product check list', 'WpAdminStyle' );
?></td>
                                            <td>

                                                <fieldset>
                                                    <legend class="screen-reader-text"> <span> Display Serial Number </span> </legend>
                                                    <label for="users_can_register">
                                                        <input name="product_list[display_serial_number]" <?php 
( isset( $wootrello_product_list['display_serial_number'] ) ? checked( $wootrello_product_list['display_serial_number'] ) : '' );
?> type="checkbox" id="display_serial_number" value="1" />
                                                        <span><?php 
esc_attr_e( 'Display Serial Number', 'WpAdminStyle' );
?></span>
                                                    </label>
                                                </fieldset>

                                                <fieldset>
                                                    <legend class="screen-reader-text"> <span> Display Product ID </span> </legend>
                                                    <label for="users_can_register">
                                                        <input name="product_list[display_product_id]" <?php 
( isset( $wootrello_product_list['display_product_id'] ) ? checked( $wootrello_product_list['display_product_id'] ) : '' );
?> type="checkbox" id="display_product_id" value="1" />
                                                        <span><?php 
esc_attr_e( 'Display Product ID ', 'WpAdminStyle' );
?></span>
                                                    </label>
                                                </fieldset>

                                                <fieldset>
                                                    <legend class="screen-reader-text"> <span> Display Product Qty </span> </legend>
                                                    <label for="users_can_register">
                                                        <input name="product_list[display_product_qty]" <?php 
( isset( $wootrello_product_list['display_product_id'] ) ? checked( $wootrello_product_list['display_product_qty'] ) : '' );
?> type="checkbox" id="display_product_qty" value="1" />
                                                        <span><?php 
esc_attr_e( 'Display Product Qty', 'WpAdminStyle' );
?></span>
                                                    </label>
                                                </fieldset>

                                                <fieldset>
                                                    <legend class="screen-reader-text"> <span> Display Product Qty * Price </span> </legend>
                                                    <label for="users_can_register">
                                                        <input name="product_list[display_product_qty_price]" <?php 
( isset( $wootrello_product_list['display_product_qty_price'] ) ? checked( $wootrello_product_list['display_product_qty_price'] ) : '' );
?> type="checkbox" id="display_product_qty_price" value="1" />
                                                        <span><?php 
esc_attr_e( 'Display Product Qty * Price', 'WpAdminStyle' );
?></span>
                                                    </label>
                                                </fieldset>
                                            
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="row-title"><?php 
esc_attr_e( 'Create Card After', 'WpAdminStyle' );
?></td>
                                            <td>

                                                <fieldset>
                                                    <legend class="screen-reader-text"><span>New Order</span></legend>
                                                    <label for="users_can_register">
                                                        <input name="create_after[new_order]" <?php 
( isset( $wootrello_create_after['new_order'] ) ? checked( $wootrello_create_after['new_order'] ) : '' );
?> type="checkbox" id="new_order" value="1" />
                                                        <span><?php 
esc_attr_e( 'New order from the checkout page.', 'WpAdminStyle' );
?></span>
                                                    </label>
                                                </fieldset>

                                                <!-- freemius starts -->
                                                <?php 
?>
                                                <!-- freemius ends -->
                                            
                                            </td>
                                        </tr>

                                    </tbody>

                                </table>

                            <!-- Table ends here  -->
                            	
						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

				</div>
				<!-- .meta-box-sortables .ui-sortable -->

			</div>
			<!-- post-body-content -->

			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">

				<div class="meta-box-sortables">

					<div class="postbox">

						<button type="button" class="handlediv" aria-expanded="true" >
							<span class="screen-reader-text">Toggle panel</span>
							<!-- <span class="toggle-indicator" aria-hidden="true"></span> -->
						</button>
						<!-- Toggle -->
						<h2 class="hndle"><span><?php 
esc_attr_e( 'Trello access code', 'WpAdminStyle' );
?></span></h2>

						<div class="inside">
							<p>
                            
								<?php 

if ( isset( $wootrello_trello_settings['trello_access_code'] ) && !empty($wootrello_trello_settings['trello_access_code']) ) {
    echo  " <input type='text' name='trello_access_code' id='trello_access_code'  value='" . $wootrello_trello_settings['trello_access_code'] . "' style='width: 100%; height: 3em;' />" ;
    echo  "<br class='clear' />" ;
    echo  "<br class='clear' />" ;
    echo  "<input class='button-secondary'  type='submit' id='save_settings' name='save_settings' value='save'  />" ;
} else {
    echo  "<a href='https://trello.com/1/authorize?expiration=never&name=Wootrello&scope=read%2Cwrite&response_type=token&key=7385fea630899510fd036b6e89b90c60'  style='margin-left:150px; text-decoration: none; ' target='_blank'>Trello access code</a>" ;
    echo  " <input type='text' name='trello_access_code' id='trello_access_code' placeholder='Pest trello access code here'  value='' style='width: 100%; height: 3em;' />" ;
    echo  "<br class='clear' />" ;
    echo  "<br class='clear' />" ;
    echo  "<input class='button-secondary'  type='submit' id='save_settings' name='save_settings' value='save access code'  />" ;
}

?>
                               
                            </p>
						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

				</div>
				<!-- .meta-box-sortables -->

				</form>
                <!-- Forms ends here  -->

                <div class="meta-box-sortables">

					<div class="postbox">

						<button type="button" class="handlediv" aria-expanded="true" >
							<span class="screen-reader-text">Toggle panel</span>
							<!-- <span class="toggle-indicator" aria-hidden="true"></span> -->
						</button>
						<!-- Toggle -->

						<h2 class="hndle"><span><?php 
esc_attr_e( 'Hello, Howdy', 'WpAdminStyle' );
?></span> <span class="dashicons dashicons-smiley"></span></h2>

						<div class="inside">
							<p>
                                <i>
                                    This Plugin has <b> 17 </b> files and  <b>2,578</b> lines of code, Trello changed its API in many ways, so I follow with the new API.
                                    Development, Testing, and Debugging takes a lot of time & patience. 
                                    I hope you will appreciate my effort. 
                                    
                                    <!-- For Paid User  -->
                                    <?php 
?>
                                        
                                    <!-- for Free and Trial  user  -->
                                    <?php 

if ( wootrello_freemius()->is_trial() || wootrello_freemius()->is_not_paying() ) {
    ?>
                                       
                                        If possible Please purchase the <?php 
    echo  '<a style="text-decoration: none;" href="' . wootrello_freemius()->get_upgrade_url() . '">' . __( ' Professional copy', 'my-text-domain' ) . '</a>' ;
    ?>, 
                                        if not please leave a <a style='text-decoration: none;' href='https://wordpress.org/support/plugin/wootrello/reviews/?filter=5'> 5-star review </a>, It will inspire me to add more awesome feature . 
                                            
                                        <br>
                                        <br> 
                                        thank you & best regards.
                                        <br>
                                        <br>
                                        <b>P.S :</b> <a style="text-decoration: none;" href=" <?php 
    echo  admin_url( 'admin.php?page=wootrello-contact' ) ;
    ?> "> let me know your questions & thoughts.</a> 
                                    
                                    <?php 
}

?>

                                </i>
                            </p>
						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

				</div>
				<!-- .meta-box-sortables -->

                <!-- Basic Version error note -->
                <?php 
if ( wootrello_freemius()->is_trial() || wootrello_freemius()->is_not_paying() ) {
    ?>
                    <span> 
                        <i> Wootrello use <a style='text-decoration: none;' href='https://github.com/woocommerce/woocommerce/blob/master/templates/checkout/thankyou.php'> woocommerce_thankyou </a> Hook for Checkout Page order so it will  <b>  not </b> work 
                        without any thank you page. please make sure you have a thank you page. 
                        </i> 
                    </span> 
                    <br>
                    <br>
                <?php 
}
?>

                <!-- Log Code Starts -->
                <span style='float:right; padding-right:25px;'> <a href="<?php 
echo  admin_url( 'admin.php?page=wootrello&action=log' ) ;
?>" style='text-decoration: none;font-style: italic;'  > log for good ! log page.  </a> </span>
                <!-- Log Code Ends -->

			</div>
			<!-- #postbox-container-1 .postbox-container -->
            
		</div>
		<!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">
	</div>
	<!-- #poststuff -->

</div> <!-- .wrap -->



