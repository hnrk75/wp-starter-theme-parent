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
