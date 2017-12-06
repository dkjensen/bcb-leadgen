<?php

if( ! defined( 'ABSPATH' ) )
    exit;


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


function mbe_set_current_menu( $parent_file ) {
    global $submenu_file, $current_screen, $pagenow;

    //print_r( $current_screen );

    print $parent_file;

    # Set the submenu as active/current while anywhere in your Custom Post Type (nwcm_news)
    if ( $current_screen->post_type == 'nwcm_news' ) {

        if ( $pagenow == 'post.php' ) {
            $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
        }

        if ( $pagenow == 'edit-tags.php' ) {
            $submenu_file = 'edit-tags.php?taxonomy=nwcm_news_category&post_type=' . $current_screen->post_type;
        }

        $parent_file = 'nwcm';

    }

    return $parent_file;

}

add_filter( 'parent_file', 'mbe_set_current_menu' );