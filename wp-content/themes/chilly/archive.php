<?php 
get_header();
$remove_banner_image = get_theme_mod('remove_banner_Archieves_image',false);
if($remove_banner_image !=true)
{
get_template_part('index','slider');
}
spicepress_breadcrumbs(); ?>
<!-- /Page Title Section -->
<div class="clearfix"></div>
<!-- Blog & Sidebar Section -->
<section class="blog-section">
	<div class="container">
		<div class="row">	
			<!--Blog Section-->
			<div class="col-md-<?php echo ( !is_active_sidebar( 'sidebar_primary' ) ? '12' :'8' ); ?> col-xs-12">
				<?php 
				if ( have_posts() ) :
					// Start the Loop.
					while ( have_posts() ) : the_post();
						// Include the post format-specific template for the content. get_post_format
						get_template_part( 'content','');
					endwhile;
					
                    // Previous/next page navigation.
					the_posts_pagination( array(
						'prev_text'          => '<i class="fa fa-angle-double-left"></i>',
						'next_text'          => '<i class="fa fa-angle-double-right"></i>'
					) );
				
				endif;
				?>
			</div>	
			<!--/Blog Section-->
			<?php get_sidebar(); ?>
		</div>
	</div>
</section>
<!-- /Blog & Sidebar Section -->

<?php get_footer(); ?>