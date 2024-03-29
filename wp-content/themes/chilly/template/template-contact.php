<?php 
/**
 * Template Name: Contact page
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
spicepress_breadcrumbs(); ?>

<!-- Contact Section -->
<section class="cont-section">
	<div class="container">
	
		<div class="row">	
			<!--Contact Form Section-->
			<?php if( get_theme_mod('contact_form_enable',true) == true ): ?>
			<div class="col-md-<?php echo ( is_active_sidebar( 'wdl_contact_page_sidebar' ) ? '8' :'12' ); ?> col-xs-12">
			<div class="cont-form-section wow fadeInDown animated animated" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInDown;">
					<?php 
				the_post();
				the_content();
				wp_link_pages( );
					?>	
			</div>
			</div>
			<?php endif; ?>
			<!--/Contact Form Section-->
			
			<!--Contact Info-->
			<?php if( get_theme_mod('contact_info_enable',true) == true ):?>
			<div class="col-md-<?php echo ( is_active_sidebar( 'wdl_contact_page_sidebar' ) ? '4' :'12' ); ?> col-xs-12">
					
				<?php 
				if( is_active_sidebar('wdl_contact_page_sidebar') ) :
					echo '<div class="sidebar">';
					dynamic_sidebar( 'wdl_contact_page_sidebar' ); 
					echo '</div>';
				endif;
				?>
				
			</div>
			<?php endif; ?>
			<!--Contact Info-->	                     
		</div>
	</div>
</section>
<!-- /Contact Section -->

<?php get_footer(); ?>