<?php
/**
 * SEO meta tags: canonical, description, Open Graph, Twitter Card and JSON-LD.
 * Off when Yoast SEO or Rank Math is active.
 *
 * @package WP Starter Theme
 */

/**
 * Whether the theme outputs its own meta tags.
 *
 * @return bool
 */
function wpst_output_meta_tags() {
	$seo_plugin_active = defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' );

	return (bool) apply_filters( 'wpst_output_meta_tags', ! $seo_plugin_active );
}

add_action(
	'wp',
	function () {
		if ( ! wpst_output_meta_tags() ) {
			return;
		}

		remove_action( 'wp_head', 'rel_canonical' );
		add_action( 'wp_head', 'wpst_meta_tags', 1 );
	}
);

// Search results already get noindex from WordPress.
add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( is_404() ) {
			$robots['noindex'] = true;
		}
		return $robots;
	}
);

/**
 * Plain text: no HTML, no entities.
 *
 * @param string $text Text that may contain HTML or entities.
 * @return string
 */
function wpst_meta_text( $text ) {
	return trim( html_entity_decode( wp_strip_all_tags( (string) $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
}

function wpst_meta_tags() {
	if ( is_search() || is_404() ) {
		return;
	}

	$paged = max( 1, (int) get_query_var( 'paged' ) );

	// --- URL / Canonical ----------------------------------------
	if ( is_front_page() ) {
		$url = $paged > 1 ? get_pagenum_link( $paged ) : home_url( '/' );
	} elseif ( is_singular() ) {
		$url = get_permalink();
	} elseif ( is_home() || is_archive() ) {
		$url = get_pagenum_link( $paged );
	} else {
		$url = home_url( add_query_arg( array() ) );
	}

	// --- Title --------------------------------------------------
	if ( is_front_page() ) {
		$title = get_bloginfo( 'name' );
	} elseif ( is_home() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		$title         = $posts_page_id ? get_the_title( $posts_page_id ) : get_bloginfo( 'name' );
	} elseif ( is_archive() ) {
		$title = get_the_archive_title();
	} else {
		$title = get_the_title();
	}
	$title = wpst_meta_text( $title );

	// --- Description --------------------------------------------
	if ( is_singular() ) {
		$description = has_excerpt()
			? get_the_excerpt()
			: wp_trim_words( strip_shortcodes( excerpt_remove_blocks( get_the_content() ) ), 30 );
	} elseif ( is_archive() ) {
		$description = get_the_archive_description();
	} else {
		$description = get_bloginfo( 'description' );
	}
	$description = wpst_meta_text( $description );

	// --- OG type ------------------------------------------------
	$og_type = is_singular( 'post' ) ? 'article' : 'website';

	// --- Image --------------------------------------------------
	$image_url    = '';
	$image_width  = '';
	$image_height = '';

	if ( is_singular() && has_post_thumbnail() ) {
		$image_data = wp_get_attachment_image_src( get_post_thumbnail_id(), 'wpst-og' );
		if ( $image_data ) {
			list( $image_url, $image_width, $image_height ) = $image_data;
		}
	}

	if ( ! $image_url ) {
		$site_icon_id = get_option( 'site_icon' );
		if ( $site_icon_id ) {
			$image_data = wp_get_attachment_image_src( $site_icon_id, 'full' );
			if ( $image_data ) {
				$image_url = $image_data[0];
			}
		}
	}

	// =============================================================
	// Output
	// =============================================================
	?>
<!-- SEO -->
<link rel="canonical" href="<?php echo esc_url( $url ); ?>">
	<?php if ( $description ) : ?>
<meta name="description" content="<?php echo esc_attr( $description ); ?>">
	<?php endif; ?>

<!-- Open Graph -->
<meta property="og:site_name" content="<?php echo esc_attr( wpst_meta_text( get_bloginfo( 'name' ) ) ); ?>">
<meta property="og:type" content="<?php echo esc_attr( $og_type ); ?>">
<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<?php if ( $description ) : ?>
<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<?php endif; ?>
	<?php if ( $image_url ) : ?>
<meta property="og:image" content="<?php echo esc_url( $image_url ); ?>">
		<?php if ( $image_width ) : ?>
<meta property="og:image:width" content="<?php echo esc_attr( (string) $image_width ); ?>">
<meta property="og:image:height" content="<?php echo esc_attr( (string) $image_height ); ?>">
	<?php endif; ?>
	<?php endif; ?>

<!-- Twitter Card -->
<meta name="twitter:card" content="<?php echo $image_url ? 'summary_large_image' : 'summary'; ?>">
<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<?php if ( $description ) : ?>
<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
	<?php endif; ?>
	<?php if ( $image_url ) : ?>
<meta name="twitter:image" content="<?php echo esc_url( $image_url ); ?>">
	<?php endif; ?>

	<?php
	wpst_json_ld( $url, $title, $description, $image_url );
}

function wpst_json_ld( $url, $title, $description, $image_url ) {
	$site_name = wpst_meta_text( get_bloginfo( 'name' ) );
	$schemas   = array();

	// --- WebSite (front page only) ------------------------------
	if ( is_front_page() ) {
		$schemas[] = array(
			'@context' => 'https://schema.org',
			'@type'    => 'WebSite',
			'name'     => $site_name,
			'url'      => home_url( '/' ),
		);
	}

	// --- Article (singular posts) --------------------------------
	// No author URL: author archives are not public.
	if ( is_singular( 'post' ) ) {
		$article = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'Article',
			'headline'      => $title,
			'url'           => $url,
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'author'        => array(
				'@type' => 'Person',
				'name'  => wpst_meta_text( get_the_author_meta( 'display_name', (int) get_post_field( 'post_author' ) ) ),
			),
			'publisher'     => array(
				'@type' => 'Organization',
				'name'  => $site_name,
				'url'   => home_url( '/' ),
			),
		);

		if ( $description ) {
			$article['description'] = $description;
		}

		if ( $image_url ) {
			$article['image'] = $image_url;
		}

		$schemas[] = $article;
	}

	// --- BreadcrumbList -----------------------------------------
	$crumbs = wpst_breadcrumb_items();
	if ( count( $crumbs ) > 1 ) {
		$list_items = array();
		foreach ( $crumbs as $index => $crumb ) {
			$list_items[] = array(
				'@type'    => 'ListItem',
				'position' => $index + 1,
				'name'     => wpst_meta_text( $crumb['name'] ),
				'item'     => $crumb['url'],
			);
		}
		$schemas[] = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $list_items,
		);
	}

	// JSON_HEX_TAG: a "</script>" in a title cannot close the tag.
	foreach ( $schemas as $schema ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
