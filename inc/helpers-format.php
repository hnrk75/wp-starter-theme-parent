<?php
/**
 * Formatting helpers for Swedish contact details: phone numbers, postcodes and email.
 *
 * Plain PHP with no output. A child theme hooks them to its own fields, e.g.
 * add_filter( 'acf/update_value/key=field_contact_phone', 'wpst_format_phone' );
 *
 * @package WP Starter Theme
 */

if ( ! function_exists( 'wpst_format_phone' ) ) {
	/**
	 * A Swedish phone number in the standard format, however it was typed:
	 * "0701234567" or "+46 70 123 45 67" → "070-123 45 67", "08 12345678" → "08-123 456 78".
	 * Foreign numbers and anything that is not a Swedish number are returned trimmed.
	 *
	 * @param string $phone Phone number as entered.
	 * @return string
	 */
	function wpst_format_phone( $phone ) {
		$phone  = trim( (string) $phone );
		$digits = preg_replace( '/\D/', '', $phone );

		if ( str_starts_with( $phone, '+46' ) || str_starts_with( $digits, '0046' ) ) {
			// "+46 (0)70…" also counts: the 0 after the country code is dropped.
			$digits = '0' . ltrim( substr( $digits, str_starts_with( $phone, '+46' ) ? 2 : 4 ), '0' );
		} elseif ( str_starts_with( $phone, '+' ) || str_starts_with( $digits, '00' ) || ! str_starts_with( $digits, '0' ) ) {
			return $phone;
		}

		// Area codes with three digits (including the 0); 08 has two and all others four.
		$area_codes_3 = array( '010', '011', '013', '016', '018', '019', '020', '021', '023', '026', '031', '033', '035', '036', '040', '042', '044', '046', '054', '060', '063', '090' );

		if ( 10 === strlen( $digits ) && preg_match( '/^07[02369]/', $digits ) ) {
			$area = 3;
		} elseif ( str_starts_with( $digits, '08' ) ) {
			$area = 2;
		} elseif ( in_array( substr( $digits, 0, 3 ), $area_codes_3, true ) ) {
			$area = 3;
		} else {
			$area = 4;
		}

		$number = substr( $digits, $area );
		$groups = array(
			5 => '/^(\d{3})(\d{2})$/',
			6 => '/^(\d{2})(\d{2})(\d{2})$/',
			7 => '/^(\d{3})(\d{2})(\d{2})$/',
			8 => '/^(\d{3})(\d{3})(\d{2})$/',
		);

		if ( ! isset( $groups[ strlen( $number ) ] ) ) {
			return $phone;
		}

		return substr( $digits, 0, $area ) . '-' . trim( preg_replace( $groups[ strlen( $number ) ], '$1 $2 $3', $number ) );
	}
}

if ( ! function_exists( 'wpst_phone_href' ) ) {
	/**
	 * A phone number as a tel: link target: "070-123 45 67" → "tel:+46701234567",
	 * "0044 20 7946 0958" → "tel:+442079460958".
	 *
	 * @param string $phone Phone number as entered.
	 * @return string Empty string when there is no number.
	 */
	function wpst_phone_href( $phone ) {
		$number = preg_replace( '/[^\d+]/', '', (string) $phone );

		if ( '' === $number ) {
			return '';
		}

		if ( str_starts_with( $number, '00' ) ) {
			$number = '+' . substr( $number, 2 );
		} elseif ( str_starts_with( $number, '0' ) ) {
			$number = '+46' . substr( $number, 1 );
		}

		return 'tel:' . $number;
	}
}

if ( ! function_exists( 'wpst_format_postcode' ) ) {
	/**
	 * A Swedish postcode in the standard format: "85234" or "852-34" → "852 34".
	 * Anything that is not five digits is returned trimmed.
	 *
	 * @param string $postcode Postcode as entered.
	 * @return string
	 */
	function wpst_format_postcode( $postcode ) {
		$digits = preg_replace( '/\D/', '', (string) $postcode );

		return 5 === strlen( $digits ) ? substr( $digits, 0, 3 ) . ' ' . substr( $digits, 3 ) : trim( (string) $postcode );
	}
}

if ( ! function_exists( 'wpst_normalize_email' ) ) {
	/**
	 * An email address trimmed and in lower case: " Info@Firma.SE " → "info@firma.se".
	 *
	 * @param string $email Email address as entered.
	 * @return string
	 */
	function wpst_normalize_email( $email ) {
		return strtolower( trim( (string) $email ) );
	}
}
