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
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> aria-labelledby="post-title-<?php the_ID(); ?>">
					<header class="entry-header">
						<h1 class="entry-title" id="post-title-<?php the_ID(); ?>">
							<?php echo esc_html( get_the_title() ); ?>
						</h1>
					</header>

					<div class="entry-content">
						<?php
						the_content();

						wp_link_pages(
							array(
								'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'wp-starter-theme' ) . '">',
								'after'  => '</nav>',
							)
						);
						?>
					</div>

					<footer class="entry-footer">
						<?php
						edit_post_link(
							esc_html__( 'Edit', 'wp-starter-theme' ),
							'<span class="edit-link">',
							'</span>'
						);
						?>
					</footer>
				</article>

				<?php
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

		<aside class="content-sidebar" aria-label="<?php echo esc_attr__( 'Sidebar', 'wp-starter-theme' ); ?>">
			<?php get_sidebar(); ?>
		</aside>
	</div>
</div>

<?php
get_footer();
