<?php

/**
 * Fired during plugin activation
 *
 * @link       http://javmah.tk
 * @since      1.0.0
 *
 * @package    Wootrello
 * @subpackage Wootrello/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wootrello
 * @subpackage Wootrello/includes
 * @author     javmah <jaedmah@gmail.com>
 */
class Wootrello_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.0.0
	 */
	public static function activate() {

		# setting installed date 
		$installed = get_option("wootrello_installed");
		if ( ! $installed ){
			update_option( "wootrello_installed", time() );		# first time installed date;
		}else{
			update_option( "wootrello_re_installed", time() );	# last time installed date;
		}

		# Coping Old access code and settings update to new one 
		$new_settings = array();
		# old access code;
		$old_wootrello_access_code = get_option( 'wootrello_access_code');
		# old settings 
		$old_wootrello_settings = get_option( 'wootrello_settings');
		
		if ( $old_wootrello_access_code  || $old_wootrello_settings  ){
			
			if( ! empty( $old_wootrello_access_code ) ){
				$new_settings['trello_access_code'] = $old_wootrello_access_code  ;
			}
			
			$old_settings = json_decode( $old_wootrello_settings , true );
			if ( $old_settings  &&  isset( $old_settings['wt_board'], $old_settings['wt_list'] ) ){
				$new_settings['trello_board'] 		 	= $old_settings['wt_board'];
				$new_settings['trello_list'] 		 	= $old_settings['wt_list'];
				$new_settings['trello_label_colour'] 	= '5e95679e7669b22549eea64e';
				$new_settings['trello_due_date']  	 	= '';
				$new_settings['trello_previous_orders'] = true;
			}
			
			# Save settings  data to the options 
			update_option('wootrello_trello_settings',  $new_settings);
			# delete old access code ;
			delete_option( "wootrello_access_code");
			# delete old wootrello settings ;
			delete_option( "wootrello_settings" );

		}
		
		# Create Card After
		update_option('wootrello_create_after', array( "new_order"=>TRUE ) );
	}

}

