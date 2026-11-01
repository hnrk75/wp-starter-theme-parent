<?php
/**
 * Searchform
 *
 * @package WP Starter Theme
 */
?>

<form role="search" method="get" class="search-form"
	action="<?php echo esc_url( home_url( '/' ) ); ?>"
	aria-label="<?php echo esc_attr__( 'Search form', 'wp-starter-theme' ); ?>">

	<div class="search-form__group">
		<label for="search-field" class="screen-reader-text">
			<?php echo esc_html__( 'Search for:', 'wp-starter-theme' ); ?>
		</label>

		<input
			type="search"
			id="search-field"
			class="search-field"
			placeholder="<?php echo esc_attr_x( 'Sök …', 'placeholder', 'wp-starter-theme' ); ?>"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			name="s"
			required
		/>

		<?php
		$icon_search = '<svg width="16" height="16" viewBox="0 0 24 24" role="img" aria-hidden="true">
			<path d="M21 21l-5.2-5.2m2.2-6.8a8 8 0 11-16 0 8 8 0 0116 0z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
		</svg>';
		wpst_button(
			array(
				'label'   => __( 'Search', 'wp-starter-theme' ),
				'variant' => 'secondary',
				'type'    => 'submit',
				'icon_html' => $icon_search,
				'icon_pos'  => 'before',
			)
		);
		?>
	</div>
</form>
