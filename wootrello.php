<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://javmah.tk
 * @since             1.0.0
 * @package           Wootrello
 *
 * @wordpress-plugin
 * Plugin Name:       jav's - WooCommerce and Trello integration
 * Plugin URI:        https://wordpress.org/plugins/wootrello/
 * Description:       WooCommerce order to Trello card  .
 * Version:           2.0.1
 * Author:            javmah
 * Author URI:        
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wootrello
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * freemius.
 * https://freemius.com/
 * freemius for monetization
 */

if ( !function_exists( 'wootrello_freemius' ) ) {
    // Create a helper function for easy SDK access.
    function wootrello_freemius()
    {
        global  $wootrello_freemius ;
        
        if ( !isset( $wootrello_freemius ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/includes/freemius/start.php';
            $wootrello_freemius = fs_dynamic_init( array(
                'id'             => '5973',
                'slug'           => 'wootrello',
                'type'           => 'plugin',
                'public_key'     => 'pk_6068691c8c430ea8c9365d8899351',
                'is_premium'     => false,
                'premium_suffix' => 'Professional',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 14,
                'is_require_payment' => true,
            ),
                'menu'           => array(
                'slug'       => 'wootrello',
                'first-path' => 'admin.php?page=wootrello',
                'support'    => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $wootrello_freemius;
    }
    
    // Init Freemius.
    wootrello_freemius();
    // Signal that SDK was initiated.
    do_action( 'wootrello_freemius_loaded' );
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WOOTRELLO_VERSION', '2.0.0' );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wootrello-activator.php
 */
function activate_wootrello()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wootrello-activator.php';
    Wootrello_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wootrello-deactivator.php
 */
function deactivate_wootrello()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wootrello-deactivator.php';
    Wootrello_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wootrello' );
register_deactivation_hook( __FILE__, 'deactivate_wootrello' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wootrello.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wootrello()
{
    $plugin = new Wootrello();
    $plugin->run();
}

run_wootrello();