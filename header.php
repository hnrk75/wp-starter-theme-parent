<?php
/**
 * The header for our theme
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @package WP Starter Theme
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main">
	<?php echo esc_html__( 'Skip to content', 'wp-starter-theme' ); ?>
</a>

<div id="page" class="site">
	<header id="masthead" class="site-header">
		<div class="container">
			<div class="site-header__inner">

				<?php
				if ( is_readable( get_theme_file_path( 'assets/svg/logo.svg' ) ) ) {
					get_template_part( 'template-parts/components/logo' );
				} elseif ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="custom-logo-link" rel="home" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
				}
				?>

				<button class="site-nav__toggle" type="button"
					aria-controls="main-nav"
					aria-expanded="false"
					aria-label="<?php echo esc_attr__( 'Open menu', 'wp-starter-theme' ); ?>">
					<span aria-hidden="true"></span>
					<span aria-hidden="true"></span>
					<span aria-hidden="true"></span>
				</button>

				<nav class="site-nav" id="main-nav" aria-label="<?php echo esc_attr__( 'Main menu', 'wp-starter-theme' ); ?>">

					<div class="site-nav__header">
						<span class="site-nav__title"><?php echo esc_html__( 'Menu', 'wp-starter-theme' ); ?></span>
						<button class="site-nav__close" type="button" aria-label="<?php echo esc_attr__( 'Close menu', 'wp-starter-theme' ); ?>">
							<span aria-hidden="true"></span>
							<span aria-hidden="true"></span>
						</button>
					</div>

					<div class="site-nav__body">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'main-menu',
								'container'      => false,
								'menu_class'     => 'site-nav__menu',
								'menu_id'        => 'main-menu',
								'fallback_cb'    => '__return_false',
								'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								'depth'          => 2,
								'walker'         => new Wpst_Navwalker(),
							)
						);
						?>
					</div>

					<div class="site-nav__footer"></div>

				</nav>

			</div>
		</div>
	</header>

	<?php
	if ( function_exists( 'wpst_the_breadcrumb' ) ) {
		$breadcrumbs = wpst_the_breadcrumb( false );
		if ( ! empty( $breadcrumbs ) ) {
			echo '<div class="container">' . wp_kses_post( $breadcrumbs ) . '</div>';
		}
	}
	?>

	<div id="content" class="site-content">
