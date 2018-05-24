<?php

if( ! defined( 'ABSPATH' ) )
    exit;


function bcb_leadgen_templates( $template ) {
    if( is_singular( 'leadpage' ) ) {
        if( empty( $template = locate_template( array( 'single-leadpage.php' ) ) ) ) {
            $template = BCB_LEADGEN_PATH . 'templates/single-leadpage.php';
        }
    }

    return $template;
}
add_filter( 'template_include', 'bcb_leadgen_templates' );