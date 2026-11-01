<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WP Starter Theme
 */

get_header(); ?>

<div class="container">
	<main
		id="main"
		class="content-main"
		tabindex="-1"
		aria-labelledby="page-title-404"
	>
		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title" id="page-title-404">
					<?php esc_html_e( 'Oops! This page could not be found.', 'wp-starter-theme' ); ?>
				</h1>
			</header>

			<div class="page-content">
				<p>
					<?php esc_html_e( 'It seems nothing was found here. You can try a search or use one of the buttons below.', 'wp-starter-theme' ); ?>
				</p>

				<?php get_search_form(); ?>

				<nav aria-label="<?php echo esc_attr__( 'Alternative links for the 404 page', 'wp-starter-theme' ); ?>">
					<?php
					wpst_button(
						array(
							'label'      => __( 'Go to homepage', 'wp-starter-theme' ),
							'variant'    => 'primary',
							'href'       => home_url( '/' ),
							'aria_label' => __( 'Go to homepage', 'wp-starter-theme' ),
						)
					);

					wpst_button(
						array(
							'label'      => __( 'Search again', 'wp-starter-theme' ),
							'variant'    => 'secondary',
							'href'       => home_url( '/?s=' ),
							'aria_label' => __( 'Search again', 'wp-starter-theme' ),
						)
					);
					?>
				</nav>
			</div>
		</section>
	</main>
</div>

<?php
get_footer();
