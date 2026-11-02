<?php
/**
 * Template part for displaying page content in page.php
 *
 * @package WP Starter Theme
 */

// A hero block first in the content must output the page's <h1> itself.
$hero_blocks         = (array) apply_filters( 'wpst_hero_blocks', array() );
$first_block_is_hero = false;
foreach ( parse_blocks( get_the_content() ) as $block ) {
	if ( ! empty( $block['blockName'] ) ) {
		$first_block_is_hero = in_array( $block['blockName'], $hero_blocks, true );
		break;
	}
}
?>

<article
	id="post-<?php the_ID(); ?>"
	<?php post_class( $first_block_is_hero ? 'has-hero-top' : '' ); ?>
	<?php if ( ! $first_block_is_hero ) : ?>
		aria-labelledby="page-title-<?php echo esc_attr( (string) get_the_ID() ); ?>"
	<?php endif; ?>
>
	<?php wpst_post_thumbnail( array( 'context' => 'page' ) ); ?>

	<?php if ( ! $first_block_is_hero ) : ?>
		<header class="entry-header">
			<?php the_title( '<h1 id="page-title-' . get_the_ID() . '" class="entry-title">', '</h1>' ); ?>
		</header>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'wp-starter-theme' ) . '"><span class="page-links-title">' . esc_html__( 'Pages:', 'wp-starter-theme' ) . '</span>',
				'after'  => '</nav>',
			)
		);
		?>
	</div>
</article>
