<?php

if( ! defined( 'ABSPATH' ) )
    exit;


function bcb_leadgen_scripts() {
    if( is_singular( 'leadpage' ) ) {
        wp_enqueue_style( 'bcb-leadgen', BCB_LEADGEN_URL . 'dist/bcb-leadgen.css', array(), BCB_LEADGEN_VER );
    }
}
add_action( 'wp_enqueue_scripts', 'bcb_leadgen_scripts', 15 );


function bcb_leadgen_admin_scripts( $hook ) {
    global $post;

    if( ( isset( $_GET['post'] ) && 'leadpage' == get_post_type( $_GET['post'] ) ) || ( isset( $_GET['page'] ) && $_GET['page'] == 'bcb-leadsys' ) || ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'leadpage' ) ) {
        wp_enqueue_style( 'bcb-leadgen-admin', BCB_LEADGEN_URL . 'dist/bcb-leadgen-admin.css', array(), BCB_LEADGEN_VER );
        wp_enqueue_script( 'bcb-leadgen-admin', BCB_LEADGEN_URL . 'dist/bcb-leadgen-admin.js', array( 'jquery' ), BCB_LEADGEN_VER );
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


/**
 * AJAX hook to save leads on the edit campaign page
 *
 * @return void
 */
function bcb_leadgen_ajax_edit_lead() {
    global $wpdb;

    check_ajax_referer( 'edit_leadpage_lead' );

    if( class_exists( 'GFFormsModel' ) ) {
        $entry_meta_table = GFFormsModel::get_entry_meta_table_name();

        $value = array_filter( array_keys( $_POST ), function( $key ) {
            return is_int( $key );
        } );

        $value = current( $value );

        if( $value ) {
            $update = $wpdb->update( $entry_meta_table, 
                array(
                    'meta_value'    => $_POST[$value]
                ),
                array(
                    'entry_id'      => $_REQUEST['entry_id'],
                    'meta_key'      => $value
                )
            );

            print $update;
        }   
    }

    exit;
}
add_action( 'wp_ajax_bcb_leadgen_edit_lead', 'bcb_leadgen_ajax_edit_lead' );