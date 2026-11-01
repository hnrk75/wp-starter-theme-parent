<?php
/**
 * The template for displaying search results pages
 *
 * @package WP Starter Theme
 */

get_header(); ?>

<div class="container">
	<main
		id="main"
		class="content-main"
		tabindex="-1"
		aria-label="<?php echo esc_attr__( 'Search results', 'wp-starter-theme' ); ?>"
	>
		<?php if ( have_posts() ) : ?>
			<header class="page-header">
				<h1 class="page-title">
					<?php
					printf(
						esc_html__( 'Search results for: %s', 'wp-starter-theme' ),
						'<span>' . esc_html( get_search_query() ) . '</span>'
					);
					?>
				</h1>
			</header>

			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content/content', 'search' );
			endwhile;

			the_posts_pagination(
				array(
					'screen_reader_text' => esc_html__( 'Page navigation', 'wp-starter-theme' ),
					'prev_text'          => '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'wp-starter-theme' ) . '</span>',
					'next_text'          => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'wp-starter-theme' ) . '</span>',
				)
			);
		else :
			get_template_part( 'template-parts/content/content', 'none' );
		endif;
		?>
	</main>
</div>

<?php
get_footer();
