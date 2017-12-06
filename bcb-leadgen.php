<?php
/**
 * Plugin Name: Broadcast Beat - Lead Generation
 * Description: Lead generation pages, categorization and more
 * Author: David Jensen
 * Author URI: https://dkjensen.com
 * Version: 1.0.0
 */

if( ! defined( 'ABSPATH' ) )
    exit;

define( 'BCB_LEADGEN_VER',   '1.0.0' );
define( 'BCB_LEADGEN_PATH',  plugin_dir_path( __FILE__ ) );
define( 'BCB_LEADGEN_URL',   plugin_dir_url( __FILE__ ) );

require_once 'includes/bcb-leadgen-post-types.php';
require_once 'includes/bcb-leadgen-templates.php';
require_once 'includes/bcb-leadgen-scripts.php';
require_once 'includes/bcb-leadgen-hooks.php';

if( is_admin() ) {
    require_once 'vendor/autoload.php';
    require_once 'includes/admin/bcb-leadgen-metaboxes.php';
}

register_activation_hook( __FILE__, function() {
    bcb_leadgen_post_types();

    flush_rewrite_rules();
} );