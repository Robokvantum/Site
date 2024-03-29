<?php 
/**
 * The main template file
 */
get_header();
$remove_banner_blog_image = get_theme_mod('remove_banner_blog_image',false);
if($remove_banner_blog_image !=true)
{
$post_id=get_the_ID();	
$chilly_data = $wpdb->get_results( "SELECT meta_key FROM wp_postmeta where post_id='$post_id' and meta_key='chilly_postbanner_chkbx'" );
			if (($wpdb->num_rows)>0) 
			{
			   if (get_post_meta( get_the_ID(), 'chilly_postbanner_chkbx', true )) 
			   {
				get_template_part('index','slider');
			   }
			}
			else {
			   get_template_part('index','slider');
			}
}
spicepress_breadcrumbs(); ?>
<!-- Blog & Sidebar Section -->
<section class="blog-section">
	<div class="container">
		<div class="row">	
			<!--Blog Section-->
			<div class="col-md-<?php echo ( !is_active_sidebar( 'sidebar_primary' ) ? '12' :'8' ); ?> col-xs-12">
				<?php 
				// Start the Loop.
				while ( have_posts() ) : the_post();
					// Include the page
					?>
					<article class="post" <?php post_class( 'post-content-area wow fadeInDown animated' ); ?> data-wow-delay="0.4s">				
					<?php 
					$blog_meta_section_enable = get_theme_mod('blog_meta_section_enable',true);
					if($blog_meta_section_enable == true) {
					spicepress_blog_meta_content(); } ?>
					<header class="entry-header">
						<?php 
						the_title( '<h3 class="entry-title">', '</h3>' );
						if($blog_meta_section_enable ==true) {
						spicepress_blog_category_content();
						}
						?>
					</header>				
					<?php 
					if(has_post_thumbnail()){
					if ( is_single() ) {
					echo '<figure class="post-thumbnail">';
					the_post_thumbnail( '', array( 'class'=>'img-responsive') );
					echo '</figure>';
					}else{
					echo '<figure class="post-thumbnail"><a class="post-thumbnail" href="'.esc_url(get_the_permalink()).'">';
					the_post_thumbnail( '', array( 'class'=>'img-responsive') );
					echo '</a></figure>';
					} } ?>
					<div class="entry-content">
					<?php
						the_content(); 
					?>
					</div>						
				</article>
					<?php
					// author meta
					spicepress_author_meta();
					
					comments_template( '', true ); // show comments 
					
				endwhile;
				?>
			</div>	
			<!--/Blog Section-->
			<?php get_sidebar(); ?>
		</div>
	</div>
</section>
<!-- /Blog & Sidebar Section -->
<?php get_footer(); ?>