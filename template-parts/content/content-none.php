<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package WP Starter Theme
 */
?>

<section class="no-results not-found" aria-labelledby="no-results-title">
	<header class="page-header">
		<h1 id="no-results-title" class="page-title">
			<?php esc_html_e( 'Nothing found', 'wp-starter-theme' ); ?>
		</h1>
	</header>

	<div class="page-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p>
				<?php
				printf(
					wp_kses(
						__( 'Ready to publish your first post? <a href="%1$s">Start here</a>.', 'wp-starter-theme' ),
						array( 'a' => array( 'href' => array() ) )
					),
					esc_url( admin_url( 'post-new.php' ) )
				);
				?>
			</p>

		<?php elseif ( is_search() ) : ?>

			<p><?php esc_html_e( 'Sorry, no results matched your search terms. Please try again with different keywords.', 'wp-starter-theme' ); ?></p>
			<?php get_search_form(); ?>

			<nav class="no-results-links" aria-label="<?php echo esc_attr__( 'Alternative when nothing found', 'wp-starter-theme' ); ?>">
				<ul>
					<li>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<?php esc_html_e( 'Go to homepage', 'wp-starter-theme' ); ?>
						</a>
					</li>
				</ul>
			</nav>

		<?php else : ?>

			<p><?php esc_html_e( 'It seems we cannot find what you are looking for. Perhaps a search can help.', 'wp-starter-theme' ); ?></p>
			<?php get_search_form(); ?>

			<nav class="no-results-links" aria-label="<?php echo esc_attr__( 'Alternative when nothing found', 'wp-starter-theme' ); ?>">
				<ul>
					<li>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<?php esc_html_e( 'Go to homepage', 'wp-starter-theme' ); ?>
						</a>
					</li>
				</ul>
			</nav>

		<?php endif; ?>
	</div>
</section>
