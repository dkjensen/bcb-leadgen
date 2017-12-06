<?php

if( ! defined( 'ABSPATH' ) )
    exit;


// Enable GF label visibility settings
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );


function bcb_leadgen_filter_text( $translated_text, $text, $context, $domain ) {
    global $post;

    if( is_admin() ) {
        if( $post && $post->post_type == 'leadpage' ) {
            if( $translated_text === 'Text' && $context == 'Name for the Text editor tab (formerly HTML)' ) {
                $translated_text = 'HTML';
            }
        }
    }

    return $translated_text;
}
add_filter( 'gettext_with_context', 'bcb_leadgen_filter_text', 10, 4 );