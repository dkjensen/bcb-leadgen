<?php

if( ! defined( 'ABSPATH' ) )
    exit;


function bcb_leadgen_scripts() {
    wp_enqueue_style( 'bcb-leadgen', BCB_LEADGEN_URL . '/assets/css/bcb-leadgen.css', array(), BCB_LEADGEN_VER );
}
add_action( 'wp_enqueue_scripts', 'bcb_leadgen_scripts', 15 );


function bcb_leadgen_gf_scripts() {
    if( is_singular( 'leadpage' ) && function_exists( 'gravity_form_enqueue_scripts' ) ) {
        gravity_form_enqueue_scripts( (int) get_post_meta( get_queried_object_id(), 'leadpage_form_id', true ), true );
    }
}
add_action( 'wp_head', 'bcb_leadgen_gf_scripts', 0 );