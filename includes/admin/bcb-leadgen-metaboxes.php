<?php

if( ! defined( 'ABSPATH' ) )
    exit;


/**
 * Register admin options page
 *
 * @return void
 */
function bcb_leadgen_admin_menu() {
    add_menu_page( __( 'Lead Manager', 'bcb-leadgen' ), __( 'Lead Manager', 'bcb-leadgen' ), 'edit_leadpages', 'bcb-leadsys', 'bcb_leadgen_leads_manager_callback', 'dashicons-chart-line', 25 );
}
add_action( 'admin_menu', 'bcb_leadgen_admin_menu', 5 );


/**
 * Lead Manager options page content
 *
 * @return void
 */
function bcb_leadgen_leads_manager_callback() {
    $lead_categories = get_terms( array(
        'taxonomy'      => 'lead_cat',
        'hide_empty'    => false,
    ) );

    $settings = get_option( 'bcb_lead_manager' );

    $settings = wp_parse_args( $settings, array(
        'admin_email'       => get_option( 'admin_email' )
    ) );

    ?>

    <div class="wrap">
        <h1><?php _e( 'Broadcast Beat Lead Manager: Campaigns', 'bcb-leadgen' ); ?></h1>
        <p><a href="<?php print admin_url( 'post-new.php?post_type=leadpage' ); ?>" class="page-title-action"><?php _e( 'Add New Lead Page', 'bcb-leadgen' ); ?></a></p>

        <div class="lead-cat-wrapper">

        <?php 
            foreach( $lead_categories as $category ) :

                $leadpages = get_posts( array( 
                    'post_type'         => 'leadpage', 
                    'posts_per_page'    => -1, 
                    'tax_query'         => array( 
                        array( 
                            'taxonomy'  => 'lead_cat', 
                            'field'     => 'term_id', 
                            'terms'     => $category->term_id 
                        ) 
                    )
                ) );
                
        ?>

        <script>

			( function( $, window, undefined ) {

				$(document).ready(function() {
					$('[name="export_lead"]').click(function () {
                        process( $(this).closest('form') );

						return false;
					});
                });
                
                function process( form, offset, exportId ) {
                    if ( typeof offset == 'undefined' ) {
						offset = 0;
					}

					if ( typeof exportId == 'undefined' ) {
						exportId = 0;
                    }
                    
                    var formId = form.find('[name="export_form"]').val();
                    var data   = form.serialize();

                    data += '&action=gf_process_export';
                    data += '&offset=' + offset;
                    data += '&exportId='+ exportId;

                    form.find('.spinner').addClass('is-active');
                    form.find(':submit').attr('disabled', 'disabled');

                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: data,
                        dataType: 'json'
                    }).done(function( response ) {
                        if ( response.status == 'in_progress' ) {
                            process( form, response.offset, response.exportId );
                        } else if ( response.status == 'complete' ) {
                            var url = ajaxurl + '?action=gf_download_export&_wpnonce=<?php echo wp_create_nonce( 'gform_download_export' ); ?>&export-id=' + response.exportId + '&form-id=' + formId;
                            document.location.href = url;

                            form.find('.spinner').removeClass('is-active');
                            form.find(':submit').removeAttr('disabled');
                        }
                    });
                }

            }( jQuery, window ));
            
        </script>

            <div class="lead-cat postbox">
                <div class="inside">
                    <h3 style="text-transform: uppercase;"><?php print $category->name; ?></h3>
                    <?php if( ! empty( $leadpages ) ) : ?>
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th scope="col" width="35%"><?php _e( 'Lead Page Title', 'bcb-leadgen' ); ?></th>
                                <th scope="col" width="15%"><?php _e( 'Lead Count', 'bcb-leadgen' ); ?></th>
                                <th scope="col" width="17.5%"><?php _e( 'Created Date', 'bcb-leadgen' ); ?></th>
                                <th scope="col" width="17.5%"><?php _e( 'Expiration Date', 'bcb-leadgen' ); ?></th>
                                <th scope="col" width="15%" style="text-align: right;"><?php _e( 'Export CSV', 'bcb-leadgen' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach( $leadpages as $leadpage ) : 
                                $form_id = get_post_meta( $leadpage->ID, 'leadpage_form_id', true );
                                $form_entries = (int) ( class_exists( 'GFAPI' ) && $form_id ) ? GFAPI::count_entries( $form_id ) : 0;
                        ?>

                            <tr>
                                <td><strong><a href="<?php print get_edit_post_link( $leadpage->ID ); ?>"><?php print get_the_title( $leadpage->ID ); ?></a></strong></td>
                                <td><?php print $form_entries; ?></td>
                                <td><?php print get_the_date( get_option( 'date_format' ), $leadpage->ID ); ?></td>
                                <td>-</td>
                                <td style="text-align: right;">
                                <?php
                                    if( class_exists( 'GFAPI' ) && $form_entries ) :
                                        $gform = GFAPI::get_form( $form_id );

                                        if( $gform && ! is_wp_error( $gform ) ) :
                                            $fields = (array) wp_list_pluck( $gform['fields'], 'id' );
                                ?>

                                    <form method="post" action="">
                                        <?php wp_nonce_field( 'rg_start_export', 'rg_start_export_nonce' ); ?>
                                        <?php array_walk( $fields, function( $value, $key ) { printf( '<input type="hidden" name="export_field[]" value="%d" />', (int) $value ); } ); ?>
                                        <input type="hidden" name="export_form" value="<?php print $form_id; ?>" />
                                        <button type="submit" name="export_lead" class="button button-primary" style="float: right;"><span class="dashicons dashicons-download" style="vertical-align: middle;"></span></button>
                                        <span class="spinner"></span>
                                    </form>

                                <?php
                                        endif;

                                    endif; 
                                ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else : ?>

                    <p><em><?php _e( 'There are no lead pages to display in this campaign.', 'bcb-leadgen' ); ?></em></p>

                    <?php endif; ?>
                </div>
            </div>

        <?php endforeach; ?>

        </div>

        <?php if( current_user_can( 'manage_options' ) ) : ?>

        <hr />

        <div class="lead-manager-settings">
            <h3><?php _e( 'Lead Manager Settings', 'bcb-leadgen' ); ?></h3>
            <form method="post" action="options.php">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label><?php _e( 'Lead Manager Email', 'bcb-leadgen' ); ?></label></th>
                            <td>
                                <input type="text" class="regular-text" name="bcb_lead_manager[admin_email]" value="<?php print esc_attr( $settings['admin_email'] ); ?>" />
                                <p class="description"><?php _e( 'Email to receive the lead generation weekly digest report.', 'bcb-leadgen' ); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <?php wp_nonce_field( 'bcb_lead_manager' ); ?>

                <?php submit_button(); ?>
            </form>
        </div>

        <?php endif; ?>
    </div>

    <?php
}


function cmb2_render_callback_for_gf_entries( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
    $form_id = get_post_meta( $object_id, 'leadpage_form_id', true );

    $form_entries = (int) ( class_exists( 'GFAPI' ) && $form_id ) ? GFAPI::count_entries( $form_id ) : 0;

    printf( '<strong>%s:</strong> %d', __( 'Total Entries', 'bcb-leadgen' ), $form_entries );


    if( class_exists( 'GFAPI' ) && $form_entries ) :
        $gform = GFAPI::get_form( $form_id );

        if( $gform && ! is_wp_error( $gform ) ) :
            $fields = (array) wp_list_pluck( $gform['fields'], 'id' );
    ?>

        <script>

			( function( $, window, undefined ) {

				$(document).ready(function() {
					$('[name="export_lead"]').click(function () {
                        process( $(this).closest('form') );

						return false;
					});
                });
                
                function process( form, offset, exportId ) {
                    if ( typeof offset == 'undefined' ) {
						offset = 0;
					}

					if ( typeof exportId == 'undefined' ) {
						exportId = 0;
                    }
                    
                    var formId = form.find('[name="export_form"]').val();
                    var data   = form.serialize();

                    data += '&action=gf_process_export';
                    data += '&offset=' + offset;
                    data += '&exportId='+ exportId;

                    form.find('.spinner').addClass('is-active');
                    form.find(':submit').attr('disabled', 'disabled');

                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: data,
                        dataType: 'json'
                    }).done(function( response ) {
                        if ( response.status == 'in_progress' ) {
                            process( form, response.offset, response.exportId );
                        } else if ( response.status == 'complete' ) {
                            var url = ajaxurl + '?action=gf_download_export&_wpnonce=<?php echo wp_create_nonce( 'gform_download_export' ); ?>&export-id=' + response.exportId + '&form-id=' + formId;
                            document.location.href = url;

                            form.find('.spinner').removeClass('is-active');
                            form.find(':submit').removeAttr('disabled');
                        }
                    });
                }

            }( jQuery, window ));
            
        </script>

        <form method="post" action="">
            <?php wp_nonce_field( 'rg_start_export', 'rg_start_export_nonce' ); ?>
            <?php array_walk( $fields, function( $value, $key ) { printf( '<input type="hidden" name="export_field[]" value="%d" />', (int) $value ); } ); ?>
            <input type="hidden" name="export_form" value="<?php print $form_id; ?>" />
            <button type="submit" name="export_lead" class="button button-primary" style="float: right;"><span class="dashicons dashicons-download" style="vertical-align: middle;"></span> <?php _e( 'Download', 'bcb-leadgen' ); ?></button>
            <span class="spinner"></span>
        </form>

    <?php
        endif;

    endif;
}
add_action( 'cmb2_render_gf_entries', 'cmb2_render_callback_for_gf_entries', 10, 5 );



function bcb_leadgen_metaboxes() {
    require_once BCB_LEADGEN_PATH . 'vendor/webdevstudios/cmb2/init.php';

    $_post = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : '';

    $prefix = 'leadpage_';

    $leadpage = new_cmb2_box( array(
        'id'            => $prefix . 'options',
        'title'         => esc_html__( 'Leadpage Options', 'bcb-leadgen' ),
        'object_types'  => array( 'leadpage' ),
        'context'       => 'after_title',
        'priority'      => 'high',
    ) );

    $leadpage->add_field( array(
        'name'       => esc_html__( 'Logo Image', 'bcb-leadgen' ),
        'desc'       => esc_html__( 'Logo to display at top of lead page', 'bcb-leadgen' ),
        'id'         => $prefix . 'logo',
        'type'       => 'file',
        'classes'    => 'col-6',
    ) );

    $leadpage->add_field( array(
        'name'       => esc_html__( 'Banner Image', 'bcb-leadgen' ),
        'desc'       => esc_html__( 'Image to display in the banner section next to the title', 'bcb-leadgen' ),
        'id'         => $prefix . 'banner',
        'type'       => 'file',
        'classes'    => 'col-6',
    ) );

    $leadpage->add_field( array(
        'name'       => esc_html__( 'Primary Color', 'bcb-leadgen' ),
        'desc'       => esc_html__( 'Background color to display behind title and main image section', 'bcb-leadgen' ),
        'id'         => $prefix . 'color_primary',
        'type'       => 'colorpicker',
        'default'    => '#0f73c3',
        'classes'    => 'col-6',
    ) );

    $leadpage->add_field( array(
        'name'       => esc_html__( 'Secondary Color', 'bcb-leadgen' ),
        'desc'       => esc_html__( 'Background color to display the header and footer bars', 'bcb-leadgen' ),
        'id'         => $prefix . 'color_secondary',
        'type'       => 'colorpicker',
        'default'    => '#222222',
        'classes'    => 'col-6',
    ) );

    $leadpage->add_field( array(
        'name'       => esc_html__( 'Company Name', 'bcb-leadgen' ),
        'id'         => $prefix . 'company',
        'type'       => 'text',
    ) );

    $leadpage->add_field( array(
        'name'       => esc_html__( 'Client Email', 'bcb-leadgen' ),
        'id'         => $prefix . 'client_email',
        'type'       => 'text_email',
    ) );

    $form = new_cmb2_box( array(
        'id'            => $prefix . 'form_options',
        'title'         => esc_html__( 'Form Options', 'bcb-leadgen' ),
        'object_types'  => array( 'leadpage' ),
    ) );

    $form->add_field( array(
        'name'    => esc_html__( 'File Download', 'bcb-leadgen' ),
        'desc'    => esc_html__( 'Upload a file which will download upon form completion', 'bcb-leadgen' ),
        'id'      => $prefix . 'form_file',
        'type'    => 'file',
        'options' => array(
            'url' => false,
        ),
        'text'    => array(
            'add_upload_file_text' => 'Add File'
        ),
        'query_args' => array(
            'type' => 'application/pdf',
        ),
        'preview_size' => 'large',
    ) );

    $form->add_field( array(
        'name'       => esc_html__( 'Form Terms', 'bcb-leadgen' ),
        'desc'       => esc_html__( 'Copy to display at the end of the lead form', 'bcb-leadgen' ),
        'id'         => $prefix . 'form_terms',
        'type'       => 'wysiwyg',
        'options'    => array(
            'media_buttons'     => false,
            'textarea_rows'     => 5,
        ),
    ) );
    
    $leadform = new_cmb2_box( array(
        'id'            => $prefix . 'leadform',
        'title'         => esc_html__( 'Lead Entries', 'bcb-leadgen' ),
        'object_types'  => array( 'leadpage' ),
        'save_fields'   => false,
        'context'       => 'side',
        'priority'      => 'high'
    ) );

    $leadform->add_field( array(
        'id'        => 'ads',
        'type'      => 'gf_entries',
    ) );
    
    $email = new_cmb2_box( array(
        'id'            => $prefix . 'email_template',
        'title'         => esc_html__( 'Email Template', 'bcb-leadgen' ),
        'object_types'  => array( 'leadpage' ),
    ) );

    $email->add_field( array(
        'name'    => esc_html__( 'Email Source', 'bcb-leadgen' ),
        'id'      => $prefix . 'email_template',
        'type'    => 'hidden',
        'after_field' => '<br><textarea rows="8" style="width: 100%;">' . bcb_leadgen_email_template( $_post ) . '</textarea>',
    ) );
}
add_action( 'cmb2_admin_init', 'bcb_leadgen_metaboxes' );


/**
 * Editable table of campaign leads
 *
 * @param WP_Post $post
 * @return void
 */
function bcb_leadgen_metaboxes_leads( $post ) {
    add_meta_box(
        'bcb_leadgen_leads_metabox',
        __( 'Leads', 'bcb-leadgen' ),
        function( $post ) {
            if( class_exists( 'GFAPI' ) ) {
                $form       = GFAPI::get_form( get_post_meta( $post->ID, 'leadpage_form_id', true ) );
                $entries    = GFAPI::get_entries( $form['id'] );
                $field_ids  = wp_list_pluck( $form['fields'], 'id' );
                $fields     = array();

                if( $form && $form['fields'] ) :
                ?>

                <table class="widefat" id="leads-form-<?php print $form['id']; ?>">
                    <thead>
                        <tr>
                            <th style="display: none;"><?php esc_html_e( 'Entry ID', 'bcb-leadgen' ); ?></th>
                            <?php foreach( $form['fields'] as $field ) : $fields[$field->id] = $field->label; ?>
                                    
                                <th><?php esc_html_e( $field->label, 'bcb-leadgen' ); ?></th>

                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach( $entries as $entry ) : ?>
                        <tr>
                            <td><?php print esc_html( $entry['id'] ); ?></td>
                            <?php
                                $entry_fields = $entry;

                                foreach( $fields as $key => $field ) {
                                    printf( '<td>%s</td>', array_key_exists( $key, $entry_fields ) ? esc_html( $entry_fields[$key] ) : '&nbsp;' );   
                                }
                            ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php

                    $columns_editable = array();
                    
                    $i = 1;
                    foreach( $fields as $key => $field ) {
                        $columns_editable[] = array(
                            $i,
                            $key
                        );  

                        $i++;
                    }

                ?>

                <script>
                    (function($) {
                        $('#leads-form-<?php print $form['id']; ?>').Tabledit({
                            url: '<?php print add_query_arg( array( 'editlead' => 'true' ), wp_nonce_url( admin_url( 'admin-ajax.php' ), 'edit_leadpage_lead' ) ); ?>',
                            editButton: false,
                            deleteButton: false,
                            hideIdentifier: true,
                            rowIdentifier: 'data-entry-id',
                            columns: {
                                identifier: [0, 'entry_id'],
                                editable: <?php print esc_js( json_encode( $columns_editable ) ); ?>
                            },
                            buttons: {
                                edit: {
                                    action: 'bcb_leadgen_edit_lead'
                                }
                            },
                            onAjax: function(action, serialize) {
                                //console.log( serialize )
                            },
                            onComplete: function(response) {

                            }
                        });
                    })(jQuery);
                </script>

                <p>&nbsp;</p>
                <hr />
                <p>&nbsp;</p>

                <?php
                endif;
            }
        },
        'leadpage',
        'normal',
        'high'

    );
}
add_action( 'add_meta_boxes_leadpage', 'bcb_leadgen_metaboxes_leads' );