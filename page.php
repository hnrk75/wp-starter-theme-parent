<?php
/**
 * The template for displaying all pages
 *
 * @package WP Starter Theme
 */

get_header(); ?>

<div class="container">
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
</div>

<?php
get_footer();
