<?php
/**
 * Template Name: With Sidebar
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

				get_template_part( 'template-parts/content/content', 'page' );

			endwhile;
			?>
		</main>

		<?php get_sidebar(); ?>
	</div>
</div>

<?php
get_footer();
