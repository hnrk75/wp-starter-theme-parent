<?php
/**
 * The main template file
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
			if ( have_posts() ) {

				if ( is_home() && ! is_front_page() ) {
					echo '<header>';
					echo '<h1 class="page-title screen-reader-text">' . single_post_title( '', false ) . '</h1>';
					echo '</header>';
				}

				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content/content', get_post_format() );
				}

				the_posts_pagination(
					array(
						'screen_reader_text' => esc_html__( 'Page navigation', 'wp-starter-theme' ),
						'prev_text'          => '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'wp-starter-theme' ) . '</span>',
						'next_text'          => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'wp-starter-theme' ) . '</span>',
					)
				);

			} else {
				get_template_part( 'template-parts/content/content', 'none' );
			}
			?>
		</main>

		<aside class="content-sidebar" aria-label="<?php echo esc_attr__( 'Sidebar', 'wp-starter-theme' ); ?>">
			<?php get_sidebar(); ?>
		</aside>
	</div>
</div>

<?php
get_footer();
