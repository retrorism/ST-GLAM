<?php
/**
 * Index main page
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php get_header('front'); ?>
<div id="primary" class="main-content">
	<div id="content" class="clearfix" role="main">
		<div id="upcoming-camps" class="clearfix feature-box">
			<h2>Upcoming THATCamps</h2>
			<article class="camp-listitem">
				<div class="camp-listdate">August 10th 2012</div>
				<h3><a href="" class="camplink">THATCamp title goes here as a link</a></h3>
				<p class="camp-listmeta">Workshops <span class="camp-listnegative">no</span></p>
			</article>
			<article class="camp-listitem">
				<div class="camp-listdate">August 10th 2012</div>
				<h3><a href="" class="camplink">THATCamp title goes here as a link</a></h3>
				<p class="camp-listmeta">Workshops <span class="camp-listnegative">no</span></p>
			</article>
			<article class="camp-listitem">
				<div class="camp-listdate">August 10th 2012</div>
				<h3><a href="" class="camplink">THATCamp title goes here as a link</a></h3>
				<p class="camp-listmeta">Workshops <span class="camp-listnegative">no</span></p>
			</article>
			<article class="camp-listitem">
				<div class="camp-listdate">August 10th 2012</div>
				<h3><a href="" class="camplink">THATCamp title goes here as a link</a></h3>
				<p class="camp-listmeta">Workshops <span class="camp-listnegative">no</span></p>
			</article>
			<article class="camp-listitem spacer-bottom">
				<div class="camp-listdate">August 10th 2012</div>
				<h3><a href="" class="camplink">THATCamp title goes here as a link</a></h3>
				<p class="camp-listmeta">Workshops <span class="camp-listnegative">no</span></p>
			</article>
			<a href="" class="button campbutton offset">
				<span class="button-inner">View all Upcoming THATCamps</span>
			</a>
		</div>
		<div id="latest-posts" class="clearfix feature-box">
			<h2>Blog posts</h2>
			<?php rewind_posts();
			while ( have_posts() ) : the_post();
				get_template_part( 'content', 'latestposts' );
			endwhile;?>
			<a href="" class="button postbutton offset">
				<span class="button-inner">View all posts</span>
			</a>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>