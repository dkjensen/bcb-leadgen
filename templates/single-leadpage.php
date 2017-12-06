<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class( 'leadpage' ); ?>>

	<?php while( have_posts() ) : the_post(); ?>

    <div class="leadpage-wrapper">
		<div class="leadpage-head">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="leadpage-logo">
							<?php
							
								if( ! empty( $logo = get_post_meta( $post->ID, 'leadpage_logo', true ) ) ) {
									printf( '<img src="%s" />', esc_url( $logo ) );
								}else {
									printf( '<h1>%s</h1>', __( 'Logo', 'bcb-leadgen' ) );
								}
							
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="leadpage-masthead">
			<div class="container">
				<div class="row">
					<div class="col-sm-6">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</div>
					<div class="col-sm-6">
						<?php

							if( ! empty( $banner = get_post_meta( $post->ID, 'leadpage_banner', true ) ) ) {
								printf( '<img src="%s" />', esc_url( $banner ) );
							}elseif( has_post_thumbnail( $post->ID ) ) {
								the_post_thumbnail( 'full' );
							}
							
						
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="leadpage-content">
			<div class="container">
				<div class="row">
					<div class="col-sm-5">
						<div class="entry-content"><?php the_content(); ?></div>
					</div>
					<div class="col-sm-6 col-sm-offset-1">
						<?php print do_shortcode( sprintf( '[gravityform id=%d title=false description=false ajax=true tabindex=10]', (int) get_post_meta( $post->ID, 'leadpage_form_id', true ) ) ); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="leadpage-foot">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						&nbsp;
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php endwhile; ?>

    <?php wp_footer(); ?>
</body>
</html>