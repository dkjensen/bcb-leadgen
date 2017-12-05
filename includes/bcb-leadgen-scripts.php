<?php

if( ! defined( 'ABSPATH' ) )
    exit;


function bcb_leadgen_scripts() {
    wp_enqueue_style( 'bcb-leadgen', BCB_LEADGEN_URL . '/assets/css/bcb-leadgen.css', array(), BCB_LEADGEN_VER );
}
add_action( 'wp_enqueue_scripts', 'bcb_leadgen_scripts', 15 );