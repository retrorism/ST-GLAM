<?php
/**
 * Content display for posts - default template
 *
 * @package bookcamp
 * @since bookcamp 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-header">
			<hgroup>
				<h1 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'bookcamp'), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
			</hgroup>
	</header>
	<?php if (( is_search() ) || /* I hate the excerpt on the home page (is_home()) || */ (is_category()) || (is_archive() ) || (is_page_template('template-blog.php') )) :?> 
		<?php if(function_exists('the_post_thumbnail')) { ?>
			<?php if(get_the_post_thumbnail() != "") { ?>
					<div class="post-featured-thumb">
						<?php the_post_thumbnail(); ?>
					</div>
		<?php } } ?>
		<div class="post-summary">
			<?php the_excerpt(); ?>
		</div>
	<?php else : ?>	
		<div class="post-body">
			<?php the_content(); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( '<span>Pages:</span>', 'bookcamp'), 'after' => '</div>' ) ); ?>
		</div>
	<?php endif; ?>
	<footer class="post-meta">
		<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
			<div class="comments-link">
				<?php comments_popup_link( __( 'Comment', 'bookcamp'), __( '1 Comment', 'bookcamp'), __( '% Comments', 'bookcamp') ); ?>
			</div>
		<?php endif; ?>
		<div class="post-author">By <?php the_author_posts_link() ?> <?php thatcamp_add_friend_button( get_the_author_ID() ) ?></div>
		<div class="post-date"><?php echo get_the_date(); ?></div>
		<div class="post-categories">
			<?php _e( 'Categories: ', 'bookcamp'); ?><?php the_category( ' ' ); ?>
		</div>
		<div class="post-tags">
			<?php $tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list ): ?>
			<?php printf( __( 'Tags: %2$s', 'bookcamp'), 'tag-links', $tags_list ); ?>
			<?php endif; ?>
		</div>
		<div class="post-edit">
				<?php edit_post_link( __( 'Edit &rarr;', 'bookcamp'), ' <span class="edit-link">', '</span> | ' ); ?>
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'bookcamp'), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php _e( 'Permalink', 'bookcamp'); ?></a>
		</div>
	</footer>
</article>
