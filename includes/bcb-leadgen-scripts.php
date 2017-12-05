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


function bcb_leadgen_create_form( $post_id, $post, $update ) {
    if( wp_is_post_revision( $post_id ) || 'auto-draft' === get_post_status( $post_id ) )
        return;

    // Make sure GF is loaded
    if( ! class_exists( 'GFAPI' ) )
        return;

    $form_id = get_post_meta( $post_id, 'leadpage_form_id', true );

    switch( get_post_status( $post_id ) ) {

        /**
         * Create or update a Gravity form
         */
        case 'publish' :
        case 'future' :
        case 'private' :
            if( empty( $form_id ) ) {
                $forms_json = file_get_contents( BCB_LEADGEN_PATH . '/includes/gravityforms/bcb-leadgen-form-default.json' );
                
                $forms = json_decode( $forms_json, true );
                
                if( $forms && is_array( $forms ) ) {
                    $form = $forms[0];

                    // Change the form title
                    $form['title'] = get_the_title( $post_id );

                    $result = GFAPI::add_form( $form );

                    if( ! is_wp_error( $result ) ) {
                        update_post_meta( $post_id, 'leadpage_form_id', $result );
                    }
                }
            }else {
                $result = GFAPI::update_form_property( $form_id, 'is_active', 1 );
            }
            break;
        
        /**
         * Set the form to inactive if the lead page is not published
         */
        case 'pending' :
        case 'draft' :
        case 'trash' :
            if( ! empty( $form_id ) ) {
                $result = GFAPI::update_form_property( $form_id, 'is_active', 0 );
            }
            break;

        default :
            //

    }
}
add_action( 'save_post_leadpage', 'bcb_leadgen_create_form', 10, 3 );