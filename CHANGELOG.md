# Changelog

All notable changes to WP Starter Theme are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the
theme uses [Semantic Versioning](https://semver.org/):

- **MAJOR** — changes that can break a child theme (removed/renamed functions, hooks, filters, classes).
- **MINOR** — new features that keep existing child themes working.
- **PATCH** — bug fixes.

Add entries under **Unreleased** as you work. `npm run release` turns that section into
the new version.

## [Unreleased]

## [3.6.2] - 2026-11-18

### Removed
- The menu button class (`menu-item-button`), the widget areas "Header, after the menu" and "Mobile menu footer", and the hooks `wpst_header_end` / `wpst_nav_footer`, all from 3.5.0 and released the same day. A standard theme has widget areas in the sidebar and footer only; a child theme styles a menu item as a button with its own CSS. The mobile menu's empty footer element is gone.

## [3.6.1] - 2026-11-18

### Removed
- `custom.logo.showTagline` and the `custom.logo.tagline` tokens from 3.5.0, released the same day: the tagline under the site name is a site's own choice. The default is the logo, else the site title, as in a regular WordPress theme; a child theme adds the tagline in its own `template-parts/header/branding.php`.

## [3.6.0] - 2026-11-18

### Added
- `template-parts/header/branding.php`: the logo or site name in the header, as a file a child theme can override instead of copying `header.php`.

### Fixed
- The SVG logo (`assets/svg/logo.svg`) had no size and shrank to nothing in the header. It now has the logo height (`--logo-height`) and the same color, hover and focus ring as the other logos.

## [3.5.1] - 2026-11-18

### Removed
- The block-based footer template part from 3.5.0 (`parts/footer.html`, `wpst-block-footer`, `wpst_block_footer()`), released the same day and not used by any site. The footer widget areas take blocks, and a child theme changes the footer with its own `footer.php`.

## [3.5.0] - 2026-11-18

### Added
- Header and menu tokens: `custom.header` (background, borderBottom, paddingBlock, marginBottom) and `custom.nav` (gap, fontSize, fontWeight, textTransform, letterSpacing, color, hoverColor, activeColor, activeFontWeight, activeIndicator, panelBackground).
- Footer tokens: `custom.footer` background, color, linkColor and paddingBlock.
- The site name without a logo is a template part (`template-parts/components/site-title.php`), and can show the tagline under the name (`custom.logo.showTagline`), styled with `custom.logo.text` and `custom.logo.tagline`.
- A menu item with the CSS class `menu-item-button` (or `menu-item-button-outline`) is shown as a button.
- Widget areas "Header, after the menu" (`header-end`) and "Mobile menu footer" (`nav-footer`), with the hooks `wpst_header_end` and `wpst_nav_footer`. The mobile menu's empty footer is no longer printed.
- Block-based footer, opt-in: with `parts/footer.html` in the child theme (or `add_theme_support( 'wpst-block-footer' )`), the footer is a template part edited under Appearance → Template Parts. The copyright row stays under it.

### Changed
- Menu links have the same color as the parent items (the text color) instead of the link color, and no link hover color; set `custom.nav.color` / `hoverColor` for other colors.
- The focus ring of buttons and menu links uses `--focus-width`.
- The text fallback for the site name has no `aria-label` any more; the visible text is its name.

## [3.4.0] - 2026-11-18

### Added
- `custom.color.background`, `text` and `heading` (`--background-color`, `--text-color`, `--heading-color`): the page background, body text and heading colors. Headings inherit the text color by default, as before.
- `custom.heading.textTransform` and `textTransform` / `color` per level (`heading.h1` … `h6`) and for `.display-1` (`custom.display`).
- `custom.link.hoverColor` and `custom.button.primaryHoverBackground` / `successHoverBackground` / `dangerHoverBackground`: hover colors in `theme.json`. The defaults are the same darker shades as before.
- `custom.logo.color` (`--logo-color`).

## [3.3.0] - 2026-11-18

The parent theme's design values are all tokens in `theme.json` now, so a child theme sets its own design there. The defaults are the same as before.

### Added
- Tokens in `settings.custom`: `measure`, `lead` (width, lineHeight), `display` and `heading.h1` … `h6` (fontWeight, lineHeight, letterSpacing), `focus` (width, offset), `button` (padding, gap, `sm`/`lg` padding and fontSize), `nav` (itemPadding, hoverBackground, submenuWidth, submenuShadow, overlay), `logo.height` and `footer.fontSize`.
- Shadow presets `sm` … `xl` in `theme.json` (`--shadow-*`), which also replace WordPress's default shadows in the editor.
- `button.radius` in `settings.custom` (`--btn-radius`): the buttons' corner radius, set without changing `radius.pill`. Default: the pill radius, as before.
- `$radius-sm`, `$radius-md` and `$radius-lg` in the SCSS abstracts.

### Changed
- `--space-*` read the `spacingSizes` in `theme.json` and `--container-max-width` reads `layout.contentSize`, so the editor's spacing and the theme's spacing are the same values.
- The skip link's transition uses `--transition-base` (0.3 s instead of 0.2 s), and the menu's transitions use it too.

## [3.2.0] - 2026-11-18

### Added
- Design tokens in `settings.custom` in `theme.json`: `radius` (sm, base, md, lg, pill), `transition.base`, `fontWeight` (regular, medium, semibold, bold) and `lineHeight` (body, heading). A child theme changes border radius, transitions, font weights and line heights in its own `theme.json`.
- README: "Public API for child themes" — the SCSS abstracts and the font files are public API, changed only in a major version.

### Changed
- `$radius-*`, `$transition-base`, `$font-weight-*` and `$line-height-*` are CSS variables instead of compiled values. The look is unchanged, but Sass can no longer compute with them (use `calc()`). A child theme that `@forward`s the parent's abstracts and defines its own `$radius-pill` no longer needs `hide $radius-pill` if it uses the token instead.

## [3.1.0] - 2026-11-18

### Added
- `--link-hover-color`, `--btn-primary-hover-bg`, `--btn-success-hover-bg` and `--btn-danger-hover-bg`: a child theme can set its own hover colors in `:root`. The defaults are the same darker shades as before.

### Changed
- The fonts come from the `theme.json` font families with the slugs `body` and `heading` (`--font-family-body` / `--font-family-heading`). A child theme changes the site's fonts in its `theme.json` alone; before, headings, the logo text and the menu title kept Fraunces.

## [3.0.0] - 2026-11-02

**Breaking:** see Removed and the `wpst_hero_blocks` change below before updating a site.

### Added
- The child theme's blocks are registered automatically: every `blocks/{name}/block.json` (folders starting with `_` are skipped). ACF blocks are only registered while ACF PRO is active.
- `wpst_footer_copyright_end` action at the end of the footer's copyright row.
- HNRK Cookie Consent's "Manage cookies" link on the copyright row, when the plugin is set to **In the footer** (the theme declares `add_theme_support( 'hnrk-cookie-consent-footer' )`).
- Styles for the Navigation Menu widget in footer widget areas.

### Changed
- `wpst_hero_blocks` is empty by default instead of `array( 'acf/hero-block' )`. A child theme with a hero block adds its block name with the filter — otherwise the page title is printed above the hero.

### Removed
- The "Footer menu" location and the menu on the copyright row. Put footer links in a **Navigation Menu** widget in a footer widget area instead (Appearance → Widgets).

## [2.0.0] - 2026-11-01

Fixes from the first client site built on the theme (svenssonsmetropol).

### Added
- `wpst_breadcrumb_items()` builds the breadcrumb trail once as data; the visible breadcrumb and the JSON-LD `BreadcrumbList` are both rendered from it. Filter: `wpst_breadcrumb_items`.
- `wpst_output_meta_tags` filter to turn off the theme's meta tags. They are turned off automatically when Yoast SEO or Rank Math is active.
- `wpst_hero_blocks` filter: which block names count as a hero block at the top of a page (default `acf/hero-block`). The hero block must then output the page's `<h1>`.
- HNRK Cookie Consent's banner links to the theme's cookie policy page (through the plugin's `hnrk_cookie_page_url` filter) once that page is published.

### Changed
- Single posts use `template-parts/content/content.php` (or `content-{post-type}.php`), so they show the featured image, date, author and tags.
- The footer shows "Add widgets here" placeholders only to users who can manage widgets; visitors see no widget section when none are active.
- The author is shown as a name without a link (in `wpst_posted_on()` and the Article schema), since author archives are not public.
- 404 pages get `noindex` through WordPress's `wp_robots` filter instead of a separate robots tag.
- `aria-current="page"` is only set on the current menu item; parents keep the `is-active` class.
- Focus indicator (`focus-visible-shadow` mixin): a solid outline in full color with a soft halo, instead of `outline: none` and a 25 % shadow. It meets WCAG 1.4.11 / 2.4.7 and is visible in Windows high contrast mode.
- The SVG logo's color rules (`currentColor`) live in the theme CSS, scoped to `.site-logo` and skipping `fill="none"`. Override them in the child theme for a multi-color logo.
- Source strings "Publicerad %s" / "av %s" / "Sök …" are now English ("Posted on %s" / "by %s" / "Search …"), translated in `sv_SE.po`.
- The theme script is deferred with WordPress's `strategy` argument.
- `wpst_icon()` allows more SVG elements and attributes: `ellipse`, gradients, `stop`, `clipPath`, `mask`, and `opacity`, `transform`, `clip-path`, `mask` on shapes.
- A disabled `wpst_button()` link has no `href`, so it cannot be clicked with the mouse either.
- The navwalker is loaded directly from `functions.php`.

### Fixed
- Breadcrumb microdata was stripped by `wp_kses_post()`, and the JSON-LD breadcrumb only had the home page and the current page. Both now show the full trail (parent pages, categories, archives).
- Year and month archives showed the month and day of the first post in the breadcrumb.
- "(Page 2)" was printed inside `<ol>` but outside `<li>` (invalid HTML); it is now its own breadcrumb item.
- Search results could get two robots tags, and contradicting ones when the site is hidden from search engines.
- Page 2 and onwards of archives and the blog had the canonical URL of page 1, so search engines would not index them.
- `og:title` / `twitter:title` could show the archive title's HTML, and JSON-LD contained HTML entities like `&#8211;`.
- JSON-LD is encoded with `JSON_HEX_TAG | JSON_HEX_AMP`, so a `</script>` in a title cannot close the script tag.
- Meta descriptions built from the post content no longer include shortcodes or block markup.
- An empty line was printed before `<!DOCTYPE html>`.
- The SVG logo's inline `<style>` applied to every SVG on the page (filling outlined icons such as the search icon). It is removed.
- The SVG logo lost `width`/`height` on inner shapes (`<rect>`, `<image>`, `<mask>`); only the `<svg>` element's size is removed now.
- The logo's `<title>` id is unique, so the logo can appear twice on a page. A missing logo file no longer prints the server's file path in an HTML comment.
- Linked post thumbnails in archives and search results were links without an accessible name (WCAG 2.4.4 / 4.1.2). The duplicate link is now hidden from screen readers and the tab order.
- The sidebar was two nested `<aside>` landmarks with the same name, and an empty landmark when it had no widgets. `sidebar.php` now outputs the only `<aside>`, and only when the sidebar has widgets.
- The search field had a fixed id, which was duplicated when the form appeared twice on a page.
- The search icon had both `role="img"` and `aria-hidden="true"`.
- `defer` on the theme script had no effect (WordPress reads `strategy`).
- The blog page title was printed without escaping.
- The 404 page's "Search again" button went to the front page (an empty search); the search form above it is enough.
- `wpst_icon()` broke the HTML when an SVG's class attribute used single quotes.
- Navigation: widening the window while the mobile menu was open left scrolling locked and the page inert; Escape in a submenu now returns focus to its button; desktop submenus close when focus leaves them; an anchor link in the mobile menu moves focus to its target instead of the menu button.

### Removed
- The "Recommended Plugins" dashboard widget and its AJAX handler, which could write to `composer.json` on the server (`wpst_get_recommended_plugins()`, `wpst_render_recommended_plugins_widget()`, `wpst_ajax_add_composer_plugin()` and related functions). The starter template already requires the plugins.
- The `SearchAction` in the WebSite schema (Google no longer uses the sitelinks search box).
- The `wp_body_open()` polyfill for WordPress < 5.2.
- Internal breadcrumb helpers `wpst_generate_breadcrumb_link()` and `wpst_handle_*()`, and `wpst_register_navwalker()`. Use `wpst_the_breadcrumb()` / `wpst_breadcrumb_items()`.

## [1.4.1] - 2026-11-01

### Added
- The repo has its own dev tools: `composer install` gives PHPCS, PHPStan and WP-CLI i18n, with `composer lint`, `composer analyze` and `composer i18n` / `composer i18n:mo`. Nothing of this is part of the package sites install.

### Changed
- README describes the theme as a standalone parent theme: Composer installation, development in a clone, and the new commands. No paths into a Bedrock project any more.
- `npm run lint:php` uses the repo's own PHPCS (`composer lint`).

## [1.4.0] - 2026-11-01

### Added
- Update row below our plugins in the plugin list (e.g. HNRK Cookie Consent), styled like WordPress's own update rows, instead of the notice at the top of the Plugins screen. The Dashboard and Themes screens keep the notice.

### Changed
- Shorter update message worded like WordPress's own ("There is a new version of … available. View version … details."), linking to the changelog.

### Fixed
- Update notices could be up to 12 hours late: a newly released version or a package added to the site was not noticed until the next check. The check now runs every hour, and right away (in the background) when the installed packages or their versions change, e.g. after `composer require`. Sites with the old twice-daily schedule are moved to hourly automatically.

## [1.3.0] - 2026-11-01

### Added
- Update notices: twice a day WP-Cron checks GitHub for newer tags of the parent theme, the security plugin and HNRK Cookie Consent (when installed), and admins see a notice on the Dashboard, Themes and Plugins screens when an update exists, with a link to the changelog. Updating is still done with Composer. More packages can be added with the `wpst_update_check_packages` filter.
- GitHub Action that tags the version in `style.css` when something is merged to `main`.

### Changed
- Recommended plugins widget: HNRK Cookie Consent is added to `composer.json` with the exact version 1.0.4 instead of `^1.0`.
- `npm run release` no longer tells you to tag by hand — tags are created by the GitHub Action.
- Theme URI and the repository in `package.json` point to the `wp-starter-theme-parent` repo.

## [1.2.0] - 2026-11-01

### Added
- `composer.json`: the theme can be installed with Composer as `hnrk75/wp-starter-theme-parent` (installed in the `wp-starter-theme` folder).

### Changed
- `npm run release` runs a production build first, so every release contains fresh CSS/JS.

## [1.1.0] - 2026-11-01

### Added
- Child theme support: the theme can be used as a parent theme. See the `wp-starter-child` template.
- Self-hosted variable fonts (Montserrat, Fraunces) registered with `fontFace` in theme.json.
- `wpst_google_fonts_url` filter to load Google Fonts if a site needs them (empty by default).
- `--measure` CSS variable for the readable line length of running text.
- Gray 50, 300, 600 and 800 in the theme.json palette.
- Recommended plugins widget: Two Factor, and a "Not activated" status with an Activate button.
- Swedish translations for missing and previously fuzzy strings, and for the theme description.
- `npm run release` and this changelog.

### Changed
- Colors are defined once in the theme.json palette and resolved in the browser, so a child theme can change them in its own theme.json. Hover colors use `color-mix()`.
- The theme's own files (`inc/`, CSS, JS) are always loaded from the parent theme.
- Line length is 42rem (~72 characters) instead of 70ch, and the ingress 45rem.
- Body text uses `hyphens: manual` instead of automatic hyphenation.
- The block editor mirrors the front end: same side padding, content width and line length.
- Links between the privacy and cookie policy pages are resolved to the pages' current URLs.

### Fixed
- Reactivating the theme no longer overwrites published or edited privacy/cookie policy pages.
- Centered and right-aligned paragraphs and paragraphs with a background use the full width.

### Removed
- Google Fonts are no longer loaded by default (GDPR).

## [1.0.0]

- Initial version.
