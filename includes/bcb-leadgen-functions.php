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


function bcb_leadgen_generate_report( $leadpage ) {
    $content = '';

    if( $leadpage ) {
        if( $leadpage === 'all' ) {
            $leadpages = get_posts( array(
                'post_type'         => 'leadpage',
                'post_status'       => 'publish',
                'posts_per_page'    => -1,
                'suppress_filters'  => true
            ) );

            foreach( (array) $leadpages as $leadpage ) {
                $form_id = get_post_meta( $leadpage->ID, 'leadpage_form_id', true );
                $form_entries = (int) ( class_exists( 'GFAPI' ) && $form_id ) ? GFAPI::count_entries( $form_id ) : 0;

                $content .= sprintf( '<p><strong>%s</strong>: %s</p>', get_the_title( $leadpage->ID ), $form_entries );
            }
        }else {
            $leadpage = get_post( $leadpage );

            if( $leadpage ) {
                $form_id = get_post_meta( $leadpage->ID, 'leadpage_form_id', true );
                $form_entries = (int) ( class_exists( 'GFAPI' ) && $form_id ) ? GFAPI::count_entries( $form_id ) : 0;

                $content = sprintf( '<p><strong>%s</strong>: %s</p>', get_the_title( $leadpage->ID ), $form_entries );
            }
        }
    }

    return $content;
}


/**
 * Generate lead generation reports to admin email and client email
 *
 * @return void
 */
function bcb_leadgen_schedule_report() {
    $leadpages = get_posts( array(
        'post_type'         => 'leadpage',
        'post_status'       => 'publish',
        'posts_per_page'    => -1,
        'suppress_filters'  => true
    ) );

    $admin_body = '';

    foreach( (array) $leadpages as $leadpage ) {
        $client_email   = get_post_meta( $leadpage->ID, 'leadpage_client_email', true );
        $form_id        = get_post_meta( $leadpage->ID, 'leadpage_form_id', true );
        $form_entries   = (int) ( class_exists( 'GFAPI' ) && $form_id ) ? GFAPI::count_entries( $form_id ) : 0;
        
        $body = sprintf( '<p><strong>%s</strong>: %s<br><strong>%s</strong>: %s</p>', 
            __( 'White paper', 'bcb-leadgen' ), 
            get_the_title( $leadpage->ID ),
            __( 'Leads', 'bcb-leadgen' ), 
            $form_entries 
        );

        $admin_body .= $body;

        if( $client_email && is_email( $client_email ) ) {
            wp_mail( $client_email, sprintf( __( 'Weekly Digest Lead Generation Report [%s]', 'bcb-leadgen' ), get_the_title( $leadpage->ID ) ), $body );
        }
    }

    if( $admin_body ) {
        wp_mail( get_option( 'admin_email' ), __( 'Weekly Digest of Lead Generation Reports', 'bcb-leadgen' ), $admin_body );
    }
}
add_action( 'bcb_leadgen_cron_leadpage_report', 'bcb_leadgen_schedule_report' );


/**
 * Add weekly occurence to cron schedules
 *
 * @param array $schedules
 * @return array
 */
function bcb_leadgen_cron_schedules( $schedules ) {
    $schedules['weekly'] = array(
		'interval' => 604800,
		'display'  => __( 'Once Weekly' )
    );
    
    return $schedules;
}
add_filter( 'cron_schedules', 'bcb_leadgen_cron_schedules' );