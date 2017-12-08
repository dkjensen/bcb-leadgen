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
                $forms_json = file_get_contents( apply_filters( 'bcb_leadgen_default_form', BCB_LEADGEN_PATH . '/includes/gravityforms/bcb-leadgen-form-default.json', $post ) );
                
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

function bcb_leadgen_leadpage_expirator( $post ) {
    global $action;

    if( 'leadpage' == get_post_type( $post->ID ) ) {

        $post_type = $post->post_type;
        $post_type_object = get_post_type_object( $post_type );

        if( current_user_can( $post_type_object->cap->publish_posts ) ) :

            $datef = __( 'M j, Y @ H:i' );
            $expires = get_post_meta( $post->ID, '_expires', true );

            if( 0 != $post->ID && ! empty( $expires ) ) {
                if ( 'future' == $post->post_status ) { // scheduled for publishing at a future date
                    /* translators: Post date information. 1: Date on which the post is currently scheduled to be published */
                    $stamp = __('Expires: <b>%1$s</b>');
                }elseif ( 'publish' == $post->post_status || 'private' == $post->post_status ) { // already published
                    /* translators: Post date information. 1: Date on which the post was published */
                    $stamp = __('Expires: <b>%1$s</b>');
                }elseif ( '0000-00-00 00:00:00' == $post->post_date_gmt ) { // draft, 1 or more saves, no date specified
                    $stamp = __('Expires: <b>immediately</b>');
                }elseif ( time() < strtotime( $post->post_date_gmt . ' +0000' ) ) { // draft, 1 or more saves, future date specified
                    /* translators: Post date information. 1: Date on which the post is to be published */
                    $stamp = __('Expires: <b>%1$s</b>');
                }else { // draft, 1 or more saves, date specified
                    /* translators: Post date information. 1: Date on which the post is to be published */
                    $stamp = __('Expires: <b>%1$s</b>');
                }

                $date = date_i18n( $datef, strtotime( $post->post_date ) );
            }else {
                $stamp = __( 'Expires: <b>never</b>' );
                $date = date_i18n( $datef, strtotime( current_time( 'mysql' ) ) );
            }
        
        ?>
            <script>
                (function($) {
                    // Edit publish time click.
                    $('#timeexpiresdiv').siblings('a.edit-timestamp').click( function( event ) {
                        console.log( 1 );
                        if ( $('#timeexpiresdiv').is( ':hidden' ) ) {
                            $('#timeexpiresdiv').slideDown( 'fast', function() {
                                $( 'input, select', $('#timeexpiresdiv').find( '.timestamp-wrap' ) ).first().focus();
                            } );
                            $(this).hide();
                        }
                        event.preventDefault();
                    });

                    // Cancel editing the publish time and hide the settings.
                    $('#timeexpiresdiv').find('.cancel-timestamp').click( function( event ) {
                        $('#timeexpiresdiv').slideUp('fast').siblings('a.edit-timestamp').show().focus();
                        $('#mm').val($('#hidden_mm').val());
                        $('#jj').val($('#hidden_jj').val());
                        $('#aa').val($('#hidden_aa').val());
                        $('#hh').val($('#hidden_hh').val());
                        $('#mn').val($('#hidden_mn').val());
                        updateText();
                        event.preventDefault();
                    });

                    // Save the changed timestamp.
                    $('#timeexpiresdiv').find('.save-timestamp').click( function( event ) { // crazyhorse - multiple ok cancels
                        if ( updateText() ) {
                            $('#timeexpiresdiv').slideUp('fast');
                            $('#timeexpiresdiv').siblings('a.edit-timestamp').show().focus();
                        }
                        event.preventDefault();
                    });
                })(jQuery);
            </script>

            <div class="misc-pub-section curtime misc-pub-curtime">
                <span id="timestamp"><?php printf( $stamp, $date ); ?></span>
                
                <a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" role="button"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit date and time' ); ?></span></a>
                
                <fieldset id="timeexpiresdiv" class="hide-if-js">
                    <legend class="screen-reader-text"><?php _e( 'Date and time' ); ?></legend>
                    <?php touch_time( ( $action === 'edit' ), 1 ); ?>
                </fieldset>
            </div>

        <?php

        endif;
    }
}
//add_action( 'post_submitbox_misc_actions', 'bcb_leadgen_leadpage_expirator', 5 );