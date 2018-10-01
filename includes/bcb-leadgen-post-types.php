<?php

if( ! defined( 'ABSPATH' ) )
    exit;


function bcb_leadgen_post_types() {
    global $wp_rewrite;

    register_taxonomy( 'lead_cat', array( 'leadpage' ), array(
        'hierarchical'      => true,
		'labels'            => array(
            'name'              => _x( 'Lead Campaigns', 'taxonomy general name', 'bcb-leadgen' ),
            'singular_name'     => _x( 'Lead Campaign', 'taxonomy singular name', 'bcb-leadgen' ),
            'search_items'      => __( 'Search Lead Campaigns', 'bcb-leadgen' ),
            'all_items'         => __( 'All Lead Campaigns', 'bcb-leadgen' ),
            'parent_item'       => __( 'Parent Lead Campaign', 'bcb-leadgen' ),
            'parent_item_colon' => __( 'Parent Lead Campaign:', 'bcb-leadgen' ),
            'edit_item'         => __( 'Edit Lead Campaign', 'bcb-leadgen' ),
            'update_item'       => __( 'Update Lead Campaign', 'bcb-leadgen' ),
            'add_new_item'      => __( 'Add New Lead Campaign', 'bcb-leadgen' ),
            'new_item_name'     => __( 'New Lead Campaign Name', 'bcb-leadgen' ),
            'menu_name'         => __( 'Lead Campaigns', 'bcb-leadgen' ),
        ),
        'show_ui'           => true,
        'show_in_menu'      => false,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'campaign' ),
    ) );

	register_post_type( 'leadpage', array(
		'labels'             => array(
            'name'               => _x( 'Lead Pages', 'post type general name', 'bcb-leadgen' ),
            'singular_name'      => _x( 'Lead Page', 'post type singular name', 'bcb-leadgen' ),
            'menu_name'          => _x( 'Lead Pages', 'admin menu', 'bcb-leadgen' ),
            'name_admin_bar'     => _x( 'Lead Page', 'add new on admin bar', 'bcb-leadgen' ),
            'add_new'            => _x( 'Add New', 'lead page', 'bcb-leadgen' ),
            'add_new_item'       => __( 'Add New Lead Page', 'bcb-leadgen' ),
            'new_item'           => __( 'New Lead Page', 'bcb-leadgen' ),
            'edit_item'          => _x( 'Edit Lead Page', 'post type edit heading', 'bcb-leadgen' ),
            'view_item'          => __( 'View Lead Page', 'bcb-leadgen' ),
            'all_items'          => __( 'Lead Pages', 'bcb-leadgen' ),
            'search_items'       => __( 'Search Lead Pages', 'bcb-leadgen' ),
            'parent_item_colon'  => __( 'Parent Lead Pages:', 'bcb-leadgen' ),
            'not_found'          => __( 'No lead pages found.', 'bcb-leadgen' ),
            'not_found_in_trash' => __( 'No lead pages found in Trash.', 'bcb-leadgen' )
        ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
        'show_in_menu'       => false,
        'show_in_rest'       => true,
		'query_var'          => true,
		'capability_type'    => 'leadpage',
		'has_archive'        => true,
		'hierarchical'       => false,
        'menu_position'      => null,
        'rewrite'            => false,
        'menu_icon'          => 'dashicons-chart-line',
		'supports'           => array( 'title', 'editor', 'author' )
    ) );
    
    $wp_rewrite->extra_permastructs['lead_cat']['struct'] = '%lead_cat%';
}
add_action( 'init', 'bcb_leadgen_post_types', -10 );


function bcb_lead_cat_base_rewrite_rules( $lead_cat_rewrite = array() ) {
    global $wp_rewrite;

    if( '' != get_option( 'permalink_structure' ) ) {
        $lead_cat_rewrite = array();

        $lead_cats = get_terms( array( 'taxonomy' => 'lead_cat', 'hide_empty' => false ) );

        foreach( $lead_cats as $lead_cat ) {
            $lead_cat_nicename = $lead_cat->slug;

            if ( $lead_cat->parent == $lead_cat->cat_ID ) {
                $lead_cat->parent = 0;
            } elseif ( $lead_cat->parent != 0 ) {
                $lead_cat_nicename = get_term_parents_list( $lead_cat->parent, 'lead_cat', array( 
                    'link' => false,
                    'format' => 'slug',
                ) );
            }

            $lead_cat_rewrite['('.$lead_cat_nicename.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?lead_cat=$matches[1]&feed=$matches[2]';
            $lead_cat_rewrite["({$lead_cat_nicename})/{$wp_rewrite->pagination_base}/?([0-9]{1,})/?$"] = 'index.php?lead_cat=$matches[1]&paged=$matches[2]';
            $lead_cat_rewrite['('.$lead_cat_nicename.')/?$'] = 'index.php?lead_cat=$matches[1]';
            $lead_cat_rewrite['('.$lead_cat_nicename.')/([^/]+)(?:/([0-9]+))?/?$'] = 'index.php?lead_cat=$matches[1]&leadpage=$matches[2]&page=$matches[3]';
        }
    }

	return $lead_cat_rewrite;
}
add_filter( 'lead_cat_rewrite_rules', 'bcb_lead_cat_base_rewrite_rules' );

add_action( 'created_lead_cat',  'flush_rewrite_rules' );
add_action( 'delete_lead_cat',   'flush_rewrite_rules' );
add_action( 'edited_lead_cat',   'flush_rewrite_rules' );