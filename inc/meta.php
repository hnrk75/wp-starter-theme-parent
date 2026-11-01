<?php
/**
 * SEO meta tags: canonical, description, Open Graph, Twitter Card, JSON-LD, robots.
 *
 * @package WP Starter Theme
 */

// Remove WordPress built-in canonical — we output our own for all contexts.
remove_action( 'wp_head', 'rel_canonical' );

function wpst_meta_tags() {

	// --- Robots -------------------------------------------------
	if ( is_search() || is_404() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
		return;
	}

	// --- URL / Canonical ----------------------------------------
	if ( is_front_page() ) {
		$url = home_url( '/' );
	} elseif ( is_home() ) {
		$url = get_permalink( get_option( 'page_for_posts' ) );
	} elseif ( is_singular() ) {
		$url = get_permalink();
	} elseif ( is_archive() ) {
		$url = get_pagenum_link( 1 );
	} else {
		$url = home_url( add_query_arg( array() ) );
	}

	// --- Title --------------------------------------------------
	if ( is_front_page() || is_home() ) {
		$title = get_bloginfo( 'name' );
	} elseif ( is_archive() ) {
		$title = get_the_archive_title();
	} else {
		$title = get_the_title();
	}

	// --- Description --------------------------------------------
	if ( is_singular() ) {
		$description = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30 );
	} elseif ( is_archive() ) {
		$description = get_the_archive_description();
	} else {
		$description = get_bloginfo( 'description' );
	}
	$description = wp_strip_all_tags( (string) $description );

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
<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
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
add_action( 'wp_head', 'wpst_meta_tags', 1 );

function wpst_json_ld( $url, $title, $description, $image_url ) {
	$site_name = get_bloginfo( 'name' );
	$schemas   = array();

	// --- WebSite (front page only) ------------------------------
	if ( is_front_page() ) {
		$schemas[] = array(
			'@context' => 'https://schema.org',
			'@type'    => 'WebSite',
			'name'     => $site_name,
			'url'      => home_url( '/' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		);
	}

	// --- Article (singular posts) --------------------------------
	if ( is_singular( 'post' ) ) {
		$author_id   = get_the_author_meta( 'ID' );
		$author_name = get_the_author_meta( 'display_name' );

		$article = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'Article',
			'headline'         => $title,
			'url'              => $url,
			'datePublished'    => get_the_date( 'c' ),
			'dateModified'     => get_the_modified_date( 'c' ),
			'author'           => array(
				'@type' => 'Person',
				'name'  => $author_name,
				'url'   => get_author_posts_url( (int) $author_id ),
			),
			'publisher'        => array(
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
	if ( ! is_front_page() && function_exists( 'wpst_the_breadcrumb' ) ) {
		$crumbs = wpst_breadcrumb_items();
		if ( ! empty( $crumbs ) ) {
			$list_items = array();
			foreach ( $crumbs as $position => $crumb ) {
				$list_items[] = array(
					'@type'    => 'ListItem',
					'position' => $position + 1,
					'name'     => $crumb['name'],
					'item'     => $crumb['url'],
				);
			}
			$schemas[] = array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $list_items,
			);
		}
	}

	foreach ( $schemas as $schema ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}

function wpst_breadcrumb_items() {
	$items = array();

	$items[] = array(
		'name' => get_bloginfo( 'name' ),
		'url'  => home_url( '/' ),
	);

	if ( is_singular() ) {
		$items[] = array(
			'name' => get_the_title(),
			'url'  => get_permalink(),
		);
	} elseif ( is_archive() ) {
		$items[] = array(
			'name' => get_the_archive_title(),
			'url'  => get_pagenum_link( 1 ),
		);
	}

	return $items;
}
