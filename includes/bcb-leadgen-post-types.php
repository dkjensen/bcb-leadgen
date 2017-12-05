<?php

if( ! defined( 'ABSPATH' ) )
    exit;


function bcb_leadgen_post_types() {
    register_taxonomy( 'lead_cat', array( 'leadpage' ), array(
        'hierarchical'      => true,
		'labels'            => array(
            'name'              => _x( 'Lead Categories', 'taxonomy general name', 'bcb-leadgen' ),
            'singular_name'     => _x( 'Lead Category', 'taxonomy singular name', 'bcb-leadgen' ),
            'search_items'      => __( 'Search Lead Categories', 'bcb-leadgen' ),
            'all_items'         => __( 'All Lead Categories', 'bcb-leadgen' ),
            'parent_item'       => __( 'Parent Lead Category', 'bcb-leadgen' ),
            'parent_item_colon' => __( 'Parent Lead Category:', 'bcb-leadgen' ),
            'edit_item'         => __( 'Edit Lead Category', 'bcb-leadgen' ),
            'update_item'       => __( 'Update Lead Category', 'bcb-leadgen' ),
            'add_new_item'      => __( 'Add New Lead Category', 'bcb-leadgen' ),
            'new_item_name'     => __( 'New Lead Category Name', 'bcb-leadgen' ),
            'menu_name'         => __( 'Lead Categories', 'bcb-leadgen' ),
        ),
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'lead-cat' ),
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
            'edit_item'          => __( 'Edit Lead Page', 'bcb-leadgen' ),
            'view_item'          => __( 'View Lead Page', 'bcb-leadgen' ),
            'all_items'          => __( 'All Lead Pages', 'bcb-leadgen' ),
            'search_items'       => __( 'Search Lead Pages', 'bcb-leadgen' ),
            'parent_item_colon'  => __( 'Parent Lead Pages:', 'bcb-leadgen' ),
            'not_found'          => __( 'No lead pages found.', 'bcb-leadgen' ),
            'not_found_in_trash' => __( 'No lead pages found in Trash.', 'bcb-leadgen' )
        ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
		'query_var'          => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
        'menu_position'      => null,
        'rewrite'            => array(
            'with_front'        => false,
        ),
        'menu_icon'          => 'dashicons-chart-line',
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' )
    ) );
}
add_action( 'init', 'bcb_leadgen_post_types' );