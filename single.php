<?php
/**
 * The template for displaying all single posts
 *
 * @package WP Starter Theme
 */

get_header(); ?>

<div class="container">
	<div class="content-layout">
		<main
			id="main"
			class="content-main"
			tabindex="-1"
			aria-label="<?php echo esc_attr__( 'Main content', 'wp-starter-theme' ); ?>"
		>
			<?php
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/content/content', get_post_type() );

				the_post_navigation(
					array(
						'screen_reader_text' => esc_html__( 'Post navigation', 'wp-starter-theme' ),
						'prev_text'          => '<span class="meta-nav" aria-hidden="true">&larr;</span> <span class="post-title">%title</span>',
						'next_text'          => '<span class="post-title">%title</span> <span class="meta-nav" aria-hidden="true">&rarr;</span>',
					)
				);
			endwhile;
			?>
		</main>

		<?php get_sidebar(); ?>
	</div>
</div>

<?php
get_footer();
