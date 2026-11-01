<?php
/**
 * Custom navwalker — Bootstrap-free, WCAG-compliant.
 *
 * @author  Henrik Pettersson
 * @package WP Starter Theme
 */

class Wpst_Navwalker extends Walker_Nav_Menu {

	private $current_item = null;

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent     = str_repeat( "\t", (int) $depth );
		$submenu_id = isset( $this->current_item->ID ) ? 'nav-submenu-' . (int) $this->current_item->ID : '';

		$output .= "\n$indent<ul"
			. ' id="' . esc_attr( $submenu_id ) . '"'
			. ' class="site-nav__submenu depth-' . (int) $depth . '"'
			. ">\n";
	}

	/**
	 * Start element output.
	 *
	 * @param WP_Nav_Menu_Item $item
	 * @param stdClass|null    $args
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$this->current_item = $item;

		$indent    = $depth ? str_repeat( "\t", (int) $depth ) : '';
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$has_child = ! empty( $args->walker->has_children );

		$is_active = (
			! empty( $item->current ) ||
			! empty( $item->current_item_ancestor ) ||
			in_array( 'current_page_parent', $item->classes, true ) ||
			in_array( 'current-post-ancestor', $item->classes, true )
		);

		$classes[] = 'site-nav__item';
		if ( $has_child ) {
			$classes[] = 'has-children';
		}
		if ( $is_active ) {
			$classes[] = 'is-active';
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$id_attr = apply_filters( 'nav_menu_item_id', 'menu-item-' . (int) $item->ID, $item, $args, $depth );
		$id_attr = $id_attr ? ' id="' . esc_attr( $id_attr ) . '"' : '';

		$output .= $indent . '<li' . $id_attr . ' class="' . esc_attr( $class_names ) . '">';

		$toggle_id  = 'nav-toggle-' . (int) $item->ID;
		$submenu_id = 'nav-submenu-' . (int) $item->ID;

		$link_class = 'site-nav__link';
		if ( $depth > 0 ) {
			$link_class .= ' site-nav__link--sub';
		}
		if ( $is_active ) {
			$link_class .= ' is-active';
		}

		$item_output = $args->before;

		if ( $has_child ) {
			$item_output .= '<button type="button"'
				. ' id="' . esc_attr( $toggle_id ) . '"'
				. ' class="' . esc_attr( $link_class ) . ' site-nav__link--parent"'
				. ' aria-expanded="false"'
				. ' aria-controls="' . esc_attr( $submenu_id ) . '"'
				. '>';
		} else {
			$href         = ! empty( $item->url ) ? ' href="' . esc_url( $item->url ) . '"' : '';
			$aria_current = $is_active ? ' aria-current="page"' : '';
			$attr_title   = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
			$target       = ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
			$rel          = '';

			if ( ! empty( $item->xfn ) ) {
				$rel = ' rel="' . esc_attr( $item->xfn ) . '"';
			}

			if ( ! empty( $item->target ) && strtolower( (string) $item->target ) === '_blank' ) {
				if ( stripos( (string) $item->xfn, 'noopener' ) === false ) {
					$existing = ! empty( $item->xfn ) ? trim( (string) $item->xfn ) . ' ' : '';
					$rel      = ' rel="' . esc_attr( $existing . 'noopener noreferrer' ) . '"';
				}
			}

			$item_output .= '<a class="' . esc_attr( $link_class ) . '"' . $href . $attr_title . $target . $rel . $aria_current . '>';
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$title        = apply_filters( 'the_title', $item->title, $item->ID );
		$item_output .= $args->link_before . esc_html( $title ) . $args->link_after;

		$item_output .= $has_child ? '</button>' : '</a>';
		$item_output .= $args->after;

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
