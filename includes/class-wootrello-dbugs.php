<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://javmah.tk
 * @since      1.0.0
 *
 * @package    wootrello_dbugs
 * @subpackage Wootrello/includes
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
 * @since      1.0.0
 * @package    wootrello_dbugs
 * @subpackage Wootrello/includes
 * @author     javmah <jaedmah@gmail.com>
 */
class wootrello_dbugs {

	# Cheack is There 
	public function check_table_exists_in_db(){
		global $wpdb;
		$table_name = $wpdb->prefix.'dbug';
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") == $table_name) {
			return true;
		}else{
			return false;
		}
	}

	# Keep Log Method || This Method Will Keep Last 5 Logs
	public function lg($zone='',$class_name='',$method_name='',$issue_type='',$description=''){
		if (!$this->check_table_exists_in_db()) {
			return ;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "dbug";
		$rt_value = $wpdb->insert($table_name, array(
			'zone' => $zone, 
			'class_name' => $class_name,
			'method_name' => $method_name,
			'issue_type' => $issue_type,
			'description' => $description,
		));

		# SQL query: Delete all records from the table except latest N.
		$wpdb->get_results("DELETE FROM ".$table_name." WHERE id NOT IN ( SELECT id FROM ( SELECT id FROM ".$table_name." ORDER BY id DESC LIMIT 5 ) foo )");
		return $rt_value ;
	}

}
