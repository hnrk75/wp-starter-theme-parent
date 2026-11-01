<?php
/**
 * Custom template tags
 *
 * @package WP Starter Theme
 */

if ( ! function_exists( 'wpst_posted_on' ) ) :
	function wpst_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time> <time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			esc_html_x( 'Publicerad %s', 'post date', 'wp-starter-theme' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		$byline = sprintf(
			esc_html_x( 'av %s', 'post author', 'wp-starter-theme' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="posted-on">' . wp_kses_post( $posted_on ) . '</span>';
		echo ' <span class="byline">' . wp_kses_post( $byline ) . '</span>';

		if ( 'post' === get_post_type() ) {
			$categories_list = get_the_category_list( ', ' );
			if ( $categories_list && wpst_categorized_blog() ) {
				printf(
					'<span class="cat-links">%s</span>',
					wp_kses_post( $categories_list )
				);
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						__( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'wp-starter-theme' ),
						array( 'span' => array( 'class' => array() ) )
					),
					esc_html( get_the_title() )
				)
			);
			echo '</span>';
		}
	}
endif;

if ( ! function_exists( 'wpst_entry_footer' ) ) :
	function wpst_entry_footer() {
		if ( 'post' === get_post_type() && is_single() ) {
			$tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list ) {
				printf(
					'<span class="tags-links">%s</span>',
					wp_kses_post( $tags_list )
				);
			}
		}

		edit_post_link(
			sprintf(
				esc_html__( 'Edit %s', 'wp-starter-theme' ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			),
			'<span class="edit-link">',
			'</span>',
			0,
			'btn btn-sm btn-secondary'
		);
	}
endif;

function wpst_categorized_blog() {
	$all_the_cool_cats = get_transient( 'wpst_categories' );

	if ( false === $all_the_cool_cats ) {
		$all_the_cool_cats = get_categories(
			array(
				'fields'     => 'ids',
				'hide_empty' => 1,
				'number'     => 2,
			)
		);
		$all_the_cool_cats = count( $all_the_cool_cats );
		set_transient( 'wpst_categories', $all_the_cool_cats );
	}

	return ( (int) $all_the_cool_cats > 1 );
}

function wpst_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	delete_transient( 'wpst_categories' );
}
add_action( 'edit_category', 'wpst_category_transient_flusher' );
add_action( 'save_post', 'wpst_category_transient_flusher' );

if ( ! function_exists( 'wpst_post_thumbnail' ) ) :
	function wpst_post_thumbnail( $args = array() ) {
		if ( ! has_post_thumbnail() ) {
			return;
		}

		$defaults = array(
			'context' => 'auto',
			'size'    => null,
			'class'   => '',
			'link'    => null,
			'eager'   => false,
		);
		$a = wp_parse_args( $args, $defaults );

		if ( 'auto' === $a['context'] ) {
			if ( is_search() ) {
				$a['context'] = 'search';
			} elseif ( is_singular( 'page' ) ) {
				$a['context'] = 'page';
			} elseif ( is_singular() ) {
				$a['context'] = 'single';
			} else {
				$a['context'] = 'archive';
			}
		}

		if ( null === $a['size'] ) {
			$context_sizes = array(
				'archive' => 'wpst-4-3',
				'search'  => 'wpst-4-3',
				'single'  => 'wpst-16-9',
				'page'    => 'wpst-hero',
			);
			$a['size'] = isset( $context_sizes[ $a['context'] ] ) ? $context_sizes[ $a['context'] ] : 'full';
		}

		$post_id  = get_the_ID();
		$thumb_id = get_post_thumbnail_id( $post_id );
		$alt_meta = $thumb_id ? get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) : '';
		$alt_meta = is_string( $alt_meta ) ? trim( $alt_meta ) : '';
		$img_class = trim( (string) $a['class'] );

		$alt_for_img = ( 'single' === $a['context'] || 'page' === $a['context'] )
			? ( $alt_meta !== '' ? $alt_meta : '' )
			: '';

		$loading = $a['eager'] ? 'eager' : 'lazy';

		$wrapper_class = 'post-thumbnail';
		if ( 'single' === $a['context'] ) {
			$full_img = get_post_meta( $post_id, '_wpst_full_featured', true );
			if ( ! empty( $full_img ) ) {
				$wrapper_class .= ' alignfull';
			}
		}

		if ( is_null( $a['link'] ) ) {
			$a['link'] = in_array( $a['context'], array( 'archive', 'search' ), true );
		}

		$img_attrs = array(
			'class'   => $img_class,
			'alt'     => $alt_for_img,
			'loading' => $loading,
		);

		echo '<div class="' . esc_attr( $wrapper_class ) . '">';

		if ( $a['link'] ) {
			echo '<a href="' . esc_url( get_permalink() ) . '">';
			the_post_thumbnail( $a['size'], $img_attrs );
			echo '</a>';
		} else {
			the_post_thumbnail( $a['size'], $img_attrs );
		}

		echo '</div>';
	}
endif;
