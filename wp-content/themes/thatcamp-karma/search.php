<?php
/**
 * Search page
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<?php do_action( 'bp_before_blog_search' ); ?>
		<div id="search-page" class="feature-box" role="main">
		<?php if ( have_posts() ) : ?>
			<header class="post-header">
					<h1 class="post-title"><?php printf( __( 'Help pages and blog posts with "%s"', 'thatcamp'), '<span>' . get_search_query() . '</span>' ); ?></h1><br />
			</header>
		<?php while ( have_posts() ) : the_post();
			get_template_part( 'parts/content', get_post_format() );
		endwhile;
			thatcamp_content_nav( 'nav-below' );
		else :
			include( 'parts/content-notfound.php' );
		endif; ?>
		</div>
		<?php do_action( 'bp_after_blog_search' ); ?>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>
