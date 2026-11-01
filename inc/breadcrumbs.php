<?php
/**
 * Breadcrumbs. The visible trail and the JSON-LD BreadcrumbList share wpst_breadcrumb_items().
 *
 * @package WP Starter Theme
 */

/**
 * Breadcrumb trail; the last item is the current page.
 *
 * @return array<int, array{name: string, url: string}> Empty on the front page.
 */
function wpst_breadcrumb_items() {
	if ( is_front_page() ) {
		return array();
	}

	$items = array(
		array(
			'name' => __( 'Home', 'wp-starter-theme' ),
			'url'  => home_url( '/' ),
		),
	);

	$add = function ( $name, $url ) use ( &$items ) {
		$items[] = array(
			'name' => wp_strip_all_tags( (string) $name ),
			'url'  => (string) $url,
		);
	};

	$add_posts_page = function () use ( $add ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		if ( $posts_page_id ) {
			$add( get_the_title( $posts_page_id ), get_permalink( $posts_page_id ) );
		}
	};

	$add_term_ancestors = function ( $term ) use ( $add ) {
		foreach ( array_reverse( get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' ) ) as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, $term->taxonomy );
			if ( $ancestor instanceof WP_Term ) {
				$add( $ancestor->name, get_term_link( $ancestor ) );
			}
		}
	};

	$current_url = function () {
		return get_pagenum_link( 1 );
	};

	if ( is_home() ) {
		$add_posts_page();
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( is_category() ) {
			$add_posts_page();
		}
		if ( $term instanceof WP_Term ) {
			$add_term_ancestors( $term );
			$label = is_category()
				/* translators: %s: category name */
				? sprintf( __( 'Category: %s', 'wp-starter-theme' ), $term->name )
				: $term->name;
			$add( $label, get_term_link( $term ) );
		}
	} elseif ( is_search() ) {
		/* translators: %s: search query */
		$add( sprintf( __( 'Search results for "%s"', 'wp-starter-theme' ), get_search_query() ), $current_url() );
	} elseif ( is_date() ) {
		$year  = (int) get_query_var( 'year' );
		$month = (int) get_query_var( 'monthnum' );
		$day   = (int) get_query_var( 'day' );

		$add( (string) $year, get_year_link( $year ) );
		if ( $month ) {
			$add( date_i18n( 'F', (int) mktime( 0, 0, 0, $month, 1, $year ) ), get_month_link( $year, $month ) );
		}
		if ( $day ) {
			$add( (string) $day, get_day_link( $year, $month, $day ) );
		}
	} elseif ( is_attachment() ) {
		$parent = get_post( (int) get_post_field( 'post_parent' ) );
		if ( $parent ) {
			$add( get_the_title( $parent ), get_permalink( $parent ) );
		}
		$add( get_the_title(), get_permalink() );
	} elseif ( is_singular() ) {
		$post_type = (string) get_post_type();

		if ( 'post' === $post_type ) {
			$add_posts_page();
			$categories = get_the_category();
			if ( ! empty( $categories ) ) {
				$add_term_ancestors( $categories[0] );
				$add( $categories[0]->name, get_category_link( $categories[0] ) );
			}
		} else {
			$post_type_object = get_post_type_object( $post_type );
			if ( 'page' !== $post_type && $post_type_object && $post_type_object->has_archive ) {
				$add( $post_type_object->labels->name, get_post_type_archive_link( $post_type ) );
			}
			foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor_id ) {
				$add( get_the_title( $ancestor_id ), get_permalink( $ancestor_id ) );
			}
		}

		$add( get_the_title(), get_permalink() );
	} elseif ( is_post_type_archive() ) {
		$add( post_type_archive_title( '', false ), get_post_type_archive_link( (string) get_query_var( 'post_type' ) ) );
	} elseif ( is_author() ) {
		$author = get_queried_object();
		if ( $author instanceof WP_User ) {
			/* translators: %s: author name */
			$add( sprintf( __( 'Articles posted by %s', 'wp-starter-theme' ), $author->display_name ), get_author_posts_url( $author->ID ) );
		}
	} elseif ( is_404() ) {
		$add( __( 'Error 404: Page not found', 'wp-starter-theme' ), $current_url() );
	}

	// From page 2, "Page N" is the current item.
	$paged = (int) get_query_var( 'paged' );
	if ( $paged > 1 && count( $items ) > 1 ) {
		/* translators: %d: page number */
		$add( sprintf( __( 'Page %d', 'wp-starter-theme' ), $paged ), get_pagenum_link( $paged ) );
	}

	return apply_filters( 'wpst_breadcrumb_items', $items );
}

/**
 * Visible breadcrumb.
 *
 * @param bool $display Echo the breadcrumb (true) or return it (false).
 * @return string|void
 */
function wpst_the_breadcrumb( $display = true ) {
	$items = wpst_breadcrumb_items();

	if ( count( $items ) < 2 ) {
		return $display ? null : '';
	}

	$last  = count( $items ) - 1;
	$parts = array();

	foreach ( $items as $index => $item ) {
		if ( $index === $last ) {
			$parts[] = '<li class="breadcrumb-item"><span class="current" aria-current="page">' . esc_html( $item['name'] ) . '</span></li>';
		} else {
			$parts[] = '<li class="breadcrumb-item"><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a></li>';
		}
	}

	$output = '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumbs', 'wp-starter-theme' ) . '">'
		. '<ol class="breadcrumb-list">'
		. implode( '<li class="delimiter" aria-hidden="true">•</li>', $parts )
		. '</ol></nav>';

	if ( ! $display ) {
		return $output;
	}

	echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Every part is escaped above.
}
