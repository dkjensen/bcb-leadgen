<?php

if( ! defined( 'ABSPATH' ) )
    exit;


function bcb_leadgen_metaboxes() {
    require_once BCB_LEADGEN_PATH . 'vendor/webdevstudios/cmb2/init.php';

    $prefix = 'leadpage_';

    $leadpage = new_cmb2_box( array(
        'id'            => $prefix . 'options',
        'title'         => esc_html__( 'Lead Page Options', 'bcb_leadgen' ),
        'object_types'  => array( 'leadpage' ),
    ) );

    $leadpage->add_field( array(
        'name'       => esc_html__( 'Logo Image', 'bcb_leadgen' ),
        'desc'       => esc_html__( 'Logo to display at top of lead page', 'bcb_leadgen' ),
        'id'         => $prefix . 'logo',
        'type'       => 'file',
    ) );

    $leadpage->add_field( array(
        'name' => esc_html__( 'Form ID', 'bcb_leadgen' ),
        'desc' => esc_html__( 'Gravity Form ID', 'bcb_leadgen' ),
        'id'   => $prefix . 'form_id',
        'type' => 'text_small',
    ) );
}
add_action( 'cmb2_admin_init', 'bcb_leadgen_metaboxes' );