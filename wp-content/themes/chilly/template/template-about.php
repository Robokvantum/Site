<?php 
/**
 * Template Name: About Us
 */
get_header();
$remove_banner_image = get_theme_mod('remove_banner_image',false);
if($remove_banner_image !=true)
{
$post_id=get_the_ID();	
$chilly_data = $wpdb->get_results( "SELECT meta_key FROM wp_postmeta where post_id='$post_id' and meta_key='chilly_banner_chkbx'" );
			if (($wpdb->num_rows)>0) 
			{
			   if (get_post_meta( get_the_ID(), 'chilly_banner_chkbx', true )) 
			   {
				get_template_part('index','slider');
			   }
			   else
			   {
			   	
			   }
			}
			else {
			   get_template_part('index','slider');
			}
}
spicepress_breadcrumbs();
if ( $post->post_content!=="" )
{?>
<!-- About Section -->
<section class="about-section">		
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-sm-6 col-xs-12">
				<?php 
				the_post();
				the_content();
				wp_link_pages( );?>
			</div>	
		</div>
	</div>
</section>
<!-- /About Section -->
<?php } ?>
<div class="clearfix"></div>

<!-- Testimonial Section -->
<?php
if ( function_exists( 'spiceb_spicepress_testimonial' ) ) {
spiceb_spicepress_testimonial();
}?>
<!-- /Testimonial Section -->
<div class="clearfix"></div>


<?php get_footer(); ?>