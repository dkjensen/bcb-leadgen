<?php

if( ! defined( 'ABSPATH' ) )
    exit;


function bcb_leadgen_email_template( $post_id = '' ) {
    if( empty( $post_id ) && ! empty( $_REQUEST['post'] ) ) {
        $post_id = $_REQUEST['post'];
    }

    if( empty( $post_id ) || ! get_post_status( $post_id ) ) {
        return __( 'Error retrieving email template', 'bcb-leadgen' );
    }

    ob_start();

    $logo       = get_post_meta( $post_id, 'leadpage_logo', true );
    $banner     = get_post_meta( $post_id, 'leadpage_banner', true );
    $title      = get_the_title( $post_id );
    $permalink  = get_permalink( $post_id );
    $content    = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );

    include BCB_LEADGEN_PATH . '/includes/email/bcb-leadgen-email-template.php';

    return apply_filters( 'bcb_leadgen_email_template', esc_textarea( ob_get_clean() ), $post_id );
}