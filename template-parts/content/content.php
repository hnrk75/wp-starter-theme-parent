<?php
/**
 * Template part for displaying posts
 *
 * @package WP Starter Theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> aria-labelledby="post-title-<?php the_ID(); ?>">

	<?php wpst_post_thumbnail( array( 'eager' => is_single() ) ); ?>

	<header class="entry-header">
		<?php
		if ( is_single() ) :
			the_title( '<h1 id="post-title-' . get_the_ID() . '" class="entry-title">', '</h1>' );
		else :
			the_title(
				sprintf(
					'<h2 id="post-title-' . get_the_ID() . '" class="entry-title"><a href="%s" rel="bookmark">',
					esc_url( get_permalink() )
				),
				'</a></h2>'
			);
		endif;
		?>
	</header>

	<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php wpst_posted_on(); ?>
		</div>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					__( 'Continue reading <span class="screen-reader-text">”%s”</span> <span aria-hidden="true">→</span>', 'wp-starter-theme' ),
					array(
						'span' => array(
							'class'       => true,
							'aria-hidden' => true,
						),
					)
				),
				esc_html( get_the_title() )
			)
		);

		wp_link_pages(
			array(
				'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'wp-starter-theme' ) . '"><span class="page-links-title">' . esc_html__( 'Pages:', 'wp-starter-theme' ) . '</span>',
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<footer class="entry-footer">
		<?php wpst_entry_footer(); ?>
	</footer>

</article>
