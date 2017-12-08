<?php

if( ! defined( 'ABSPATH' ) )
    exit;


// Enable GF label visibility settings
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );


function bcb_leadgen_filter_gf_form( $form_string, $form ) {
    $form_string = $form_string . '123';

    return $form_string;
}
add_filter( 'gform_get_form_filter', 'bcb_leadgen_filter_gf_form', 10, 2 );


function bcb_leadgen_filter_text( $translated_text, $text, $context, $domain ) {
    if( is_admin() ) {
        if( ( isset( $_GET['post'] ) && 'leadpage' == get_post_type( $_GET['post'] ) ) || ( isset( $_GET['page'] ) && $_GET['page'] == 'bcb-leadsys' ) || ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'leadpage' ) ) {
            if( $translated_text == 'Text' && $context == 'Name for the Text editor tab (formerly HTML)' ) {
                $translated_text = 'HTML';
            }

            if( $translated_text == 'Edit Lead Page' && $context == 'post type edit heading' && isset( $_GET['post'] ) ) {
                $lead_cats = get_the_term_list( (int) $_GET['post'], 'lead_cat', __( 'Lead Campaign: ', 'bcb-leadgen' ), ', ' );

                if( ! empty( $lead_cats ) ) {
                    $translated_text = sprintf( __( 'Edit Lead Page | %s', 'bcb-leadgen' ), $lead_cats );
                }
            }
        }
    }

    return $translated_text;
}
add_filter( 'gettext_with_context', 'bcb_leadgen_filter_text', 10, 4 );