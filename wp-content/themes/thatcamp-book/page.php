<?php
/**
 * Template Name : Page
 *
 * @package bookcamp
 * @since bookcamp 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<div id="page" role="main">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
			get_template_part( 'parts/content', 'page' ); 
			comments_template( '', true ); 			
		endwhile; ?>		
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
