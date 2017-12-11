<?php

if( ! defined( 'ABSPATH' ) )
    exit;


function bcb_leadgen_scripts() {
    if( is_singular( 'leadpage' ) ) {
        wp_enqueue_style( 'bcb-leadgen', BCB_LEADGEN_URL . '/assets/css/bcb-leadgen.css', array(), BCB_LEADGEN_VER );
    }
}
add_action( 'wp_enqueue_scripts', 'bcb_leadgen_scripts', 15 );


function bcb_leadgen_admin_scripts( $hook ) {
    global $post;

    if( ( isset( $_GET['post'] ) && 'leadpage' == get_post_type( $_GET['post'] ) ) || ( isset( $_GET['page'] ) && $_GET['page'] == 'bcb-leadsys' ) || ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'leadpage' ) ) {
        wp_enqueue_style( 'bcb-leadgen-admin', BCB_LEADGEN_URL . '/assets/css/bcb-leadgen-admin.css', array(), BCB_LEADGEN_VER );
    }
}
add_action( 'admin_enqueue_scripts', 'bcb_leadgen_admin_scripts', 15 );

function bcb_leadgen_gf_scripts() {
    if( is_singular( 'leadpage' ) && function_exists( 'gravity_form_enqueue_scripts' ) ) {
        gravity_form_enqueue_scripts( (int) get_post_meta( get_queried_object_id(), 'leadpage_form_id', true ), true );
    }
}
add_action( 'wp_head', 'bcb_leadgen_gf_scripts', 0 );


function bcb_leadgen_single_css() {
    if( is_singular( 'leadpage' ) ) {
        $primary_color   = get_post_meta( get_queried_object_id(), 'leadpage_color_primary', true );
        $secondary_color = get_post_meta( get_queried_object_id(), 'leadpage_color_secondary', true );
        ?>

            <style>
                .leadpage-wrapper .leadpage-head,
                .leadpage-wrapper .leadpage-foot {
                    <?php print ( ! empty( $secondary_color ) ? sprintf( 'background: %s;', esc_attr( $secondary_color ) ) : '' ); ?>
                }
                .leadpage-wrapper .leadpage-masthead {
                    <?php print ( ! empty( $primary_color ) ? sprintf( 'background: %s;', esc_attr( $primary_color ) ) : '' ); ?>
                }
            </style>

        <?php
    }
}
add_action( 'wp_head', 'bcb_leadgen_single_css', 20 );
