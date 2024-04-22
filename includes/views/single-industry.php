<?php get_header(); ?>

<div class="fl-content-full container">
	<div class="row">
		<div class="fl-content col-md-12">
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
				<article <?php post_class( 'fl-post' ); ?> id="fl-post-<?php the_ID(); ?>" itemscope="itemscope" itemtype="http://schema.org/CreativeWork">

					<div class="fl-post-content clearfix" itemprop="text">
						<?php the_content(); ?>
					</div><!-- .fl-post-content -->

				</article>
				<!-- .fl-post -->
			<?php endwhile; endif; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>