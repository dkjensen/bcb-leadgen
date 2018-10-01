<?php
/**
 * Plugin Name: Broadcast Beat - Lead Manager
 * Description: Lead generation pages, categorization and more
 * Author: David Jensen
 * Author URI: https://dkjensen.com
 * Version: 1.0.2
 */

if( ! defined( 'ABSPATH' ) )
    exit;

define( 'BCB_LEADGEN_VER',   '1.0.2' );
define( 'BCB_LEADGEN_PATH',  plugin_dir_path( __FILE__ ) );
define( 'BCB_LEADGEN_URL',   plugin_dir_url( __FILE__ ) );

require_once 'includes/bcb-leadgen-post-types.php';
require_once 'includes/bcb-leadgen-templates.php';
require_once 'includes/bcb-leadgen-scripts.php';
require_once 'includes/bcb-leadgen-hooks.php';
require_once 'includes/bcb-leadgen-filters.php';
require_once 'includes/bcb-leadgen-functions.php';

if( is_admin() ) {
    require_once 'vendor/autoload.php';
    require_once 'includes/admin/bcb-leadgen-metaboxes.php';
}

register_activation_hook( __FILE__, function() {
    bcb_leadgen_post_types();
    bcb_lead_cat_base_rewrite_rules();

    if( ! wp_next_scheduled( 'bcb_leadgen_cron_leadpage_report' ) ) {
        wp_schedule_event( strtotime( 'Friday 08:00 ' . get_option( 'timezone_string' ) ), 'weekly', 'bcb_leadgen_cron_leadpage_report' );
    }

    $admin = get_role( 'administrator' );
    $admin->add_cap( 'edit_leadpage' );
    $admin->add_cap( 'read_leadpage' );
    $admin->add_cap( 'delete_leadpage' );
    $admin->add_cap( 'edit_leadpages' );
    $admin->add_cap( 'edit_others_leadpages' );
    $admin->add_cap( 'publish_leadpages' );
    $admin->add_cap( 'read_private_leadpages' );
    $admin->add_cap( 'edit_leadpages' );

    // Add our Lead Manager role
    add_role( 'ad_lead_manager', __( 'Ad/Lead Manager', 'bcb-leadgen' ), array( 'read' => true ) );

    $ad_lead_manager = get_role( 'ad_lead_manager' );
    $ad_lead_manager->add_cap( 'edit_leadpage' );
    $ad_lead_manager->add_cap( 'read_leadpage' );
    $ad_lead_manager->add_cap( 'delete_leadpage' );
    $ad_lead_manager->add_cap( 'edit_leadpages' );
    $ad_lead_manager->add_cap( 'edit_others_leadpages' );
    $ad_lead_manager->add_cap( 'publish_leadpages' );
    $ad_lead_manager->add_cap( 'read_private_leadpages' );
    $ad_lead_manager->add_cap( 'edit_leadpages' );

    flush_rewrite_rules();
} );


register_deactivation_hook(__FILE__,  function() {
    remove_filter( 'lead_cat_rewrite_rules', 'bcb_lead_cat_base_rewrite_rules' );

    flush_rewrite_rules();
} );