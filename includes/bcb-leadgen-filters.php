<?php

if( ! defined( 'ABSPATH' ) )
    exit;


// Enable GF label visibility settings
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );


function bcb_leadgen_filter_gf_form( $form_string, $form ) {
    if( is_singular( 'leadpage' ) ) {
        $terms = get_post_meta( get_queried_object_id(), 'leadpage_form_terms', true );

        if( ! empty( $terms ) && class_exists( 'DOMDocument' ) ) {
            $dom = new DOMDocument();
            $dom->loadHTML( $form_string );

            $formEl = $dom->getElementById( 'gform_' . $form['id'] );

            $terms = $dom->createElement( 'div', $terms ); 
            $terms = $formEl->appendChild( $terms );
            $terms->setAttribute( 'class', 'gform_terms' );

            $form_string = $dom->saveHTML();
        }
    }

    return $form_string;
}
add_filter( 'gform_get_form_filter', 'bcb_leadgen_filter_gf_form', 10, 2 );


function bcb_leadgen_form_file_download( $confirmation, $form, $entry, $ajax ) {
    if( is_singular( 'leadpage' ) ) {
        $download = get_post_meta( get_queried_object_id(), 'leadpage_form_file_id', true );
        
        $terms = wp_get_post_terms( get_queried_object_id(), 'lead_cat' );
    
        if( 0 < count( $terms ) ) {
            $redirect = get_term_link( $terms[0]->term_id );
        }else {
            $redirect = home_url();
        }

        $confirmation .= "<br>" . __( 'You will automatically be redirected in 5 seconds...', 'bcb_leadgen' );

        if( ! empty( $download ) ) {
            $url = add_query_arg( array( 'bcb-leadgen-dl' => 1, '_wpnonce' => wp_create_nonce( 'rg_bcb_leadgen_download' ) ), get_permalink( get_queried_object_id() ) );

            $confirmation .= "<script>window.top.jQuery( document ).ready(function() {
                window.top.jQuery(document).bind('gform_confirmation_loaded', function () {document.location.href = '" . $url . "';});
            });
            </script>";
        }

        $confirmation .= "<script>window.setTimeout(function(){ window.location.href = '" . $redirect . "'; }, 5000);</script>";
    }

    return $confirmation;
}
add_filter( 'gform_confirmation', 'bcb_leadgen_form_file_download', 10, 4 );


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


function bcb_leadgen_leadpage_link( $permalink, $post ) {
    if( $post->post_type !== 'leadpage' )
        return $permalink;

    $terms = wp_get_post_terms( $post->ID, 'lead_cat' );

    if( 0 < count( $terms ) ) {
        $location = $terms[0]->slug;
    }else {
        $location = apply_filters( 'bcb_leadgen_leadpage_default_base', 'uncategorized', $post );
    }

    $permalink = trailingslashit( home_url( urlencode( $location ) . '/' . $post->post_name ) );

    return $permalink;
}
add_filter( 'post_type_link', 'bcb_leadgen_leadpage_link', 10, 2 );


add_shortcode( 'gform_embed_title', function( $atts, $content = '' ) {
    return $content;
} );


add_filter( 'gform_field_choices', function( $choices, $field ) {
    $choices = str_replace( '[gform_embed_title]', get_the_title( get_queried_object_id() ), $choices );

    return $choices;
}, 10, 2 );