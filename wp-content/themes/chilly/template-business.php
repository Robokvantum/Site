<?php 
		// Template Name: Business Template
		get_header();
		$remove_banner_image = get_theme_mod('home_page_slider_enabled');
		if($remove_banner_image !="off")
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
		do_action( 'spiceb_spicepress_sections', false );
		get_template_part('index','news');
		get_footer();
?>
		