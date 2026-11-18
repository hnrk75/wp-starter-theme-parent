# WP Starter Theme

Parent theme for client sites, built with SCSS (ITCSS) and Gulp. Sites install it with
Composer and do all site-specific work in a child theme, so the theme can be updated.
Follows WordPress Coding Standards (WPCS) with built-in linting for PHP, JS, and SCSS.

---

## Theme information

**Theme Name:** WP Starter Theme
**Version:** see `Version` in `style.css` (the value WordPress shows)
**Author:** Henrik Pettersson
**License:** GNU General Public License v2 or later
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

---

## Description

A responsive WordPress starter theme with a clean ITCSS/BEM SCSS architecture, fluid typography
via theme.json, a Gulp 5 build pipeline, and full accessibility (WCAG) support.
No Bootstrap — custom container system, spacing scale with clamp(), and self-hosted fonts.

---

## Requirements

| Dependency | Minimum |
|------------|---------|
| WordPress  | 7.1     |
| PHP        | 8.4     |
| Node.js    | 24.0    |
| npm        | 11.0    |
| Composer   | 2.0     |

---

## Features

- SCSS ITCSS architecture with @use/@forward
- Fluid typography via theme.json (clamp-based font sizes)
- Fluid spacing scale (--space-xs to --space-xl)
- Gulp 5 build system — CSS, JS, BrowserSync
- Self-hosted variable fonts (Montserrat + Fraunces) via theme.json `fontFace` — no requests to Google
- Custom container system in rem
- BEM methodology with consistent nesting
- WCAG-compliant navigation with custom navwalker
- Gutenberg support — editor-style, theme.json, alignment classes
- Block editor mirrors the front end — same side padding, content width and line length
- Readable line length for running text via a single `--measure` variable
- Accessibility — skip link, aria-labels, screen-reader-text, focus-visible, no automatic hyphenation
- Code linting via ESLint, Stylelint, Prettier, PHPCS and PHPStan
- Update notices for our Composer packages (theme, plugins) — on the Dashboard and as an update row in the plugin list
- Auto-generated privacy policy and cookie policy pages on theme activation
- i18n-ready with Swedish (sv_SE) translation included

---

## Installation

Install it in a Bedrock site with an exact version:

```json
"repositories": [
  { "type": "vcs", "url": "https://github.com/hnrk75/wp-starter-theme-parent" }
],
"require": {
  "hnrk75/wp-starter-theme-parent": "2.0.0"
}
```

It is installed in `web/app/themes/wp-starter-theme/` (the folder name child themes
expect in `Template:`). New sites start from the
[wp-starter-theme](https://github.com/hnrk75/wp-starter-theme) template, which already
requires it and contains a child theme. Activate the child theme, not this one.

---

## Development

Work in a clone of this repo. To see changes in a site, link the clone into the
starter template's Local site with `composer dev:link` (see the template's README).

```bash
composer install   # PHP dev tools: PHPCS, PHPStan, WP-CLI i18n (dev only)
npm install        # Build tools
npm run dev        # Watch + BrowserSync
npm run build      # Production build
npm run lint       # Lint JS, CSS and PHP
npm run fix        # Auto-fix JS, CSS and PHP
composer analyze   # PHPStan
```

---

## Versioning & releases

The theme uses [Semantic Versioning](https://semver.org/) (`MAJOR.MINOR.PATCH`):

| Change | Bump | Example |
|--------|------|---------|
| Bug fix | `patch` | 1.1.0 → 1.1.1 |
| New feature, child themes keep working | `minor` | 1.1.1 → 1.2.0 |
| Can break a child theme (removed/renamed function, hook, filter or class) | `major` | 1.2.0 → 2.0.0 |

Workflow:

1. While working, add a line under **Unreleased** in `CHANGELOG.md`
   (Added / Changed / Fixed / Removed / Security).
2. Release:
   ```bash
   npm run release -- minor    # or patch, major, or an exact version like 1.4.0
   ```
   This updates `style.css`, `package.json`, `package-lock.json` and moves the
   Unreleased entries to the new version in `CHANGELOG.md`. It stops if Unreleased is empty.
3. Commit on `dev` and push, then PR `dev` → `main` with **Create a merge commit**.
4. The **Tag release** GitHub Action tags the version from `style.css` (e.g. `v1.3.0`) on
   `main`. Never create version tags by hand — the tag must match `style.css`.
   Composer reads the git tags as versions.

### Public API for child themes

Changing any of these is a **major** version:

- PHP functions, hooks and filters (`wpst_*`), and the templates in `template-parts/`.
- `assets/scss/abstracts/`: the variables (`$color-*`, `$font-*`, `$radius-*`,
  `$space-*`, `$breakpoints` …) and mixins (`mq-up()`, `focus-visible-shadow()`, `link-underline()` …).
  Child themes `@forward` them in their own SCSS build, so they stay in the package.
- The CSS custom properties in `base/_root.scss` and the `theme.json` slugs and
  `settings.custom` keys they read.
- The font files in `assets/fonts/`. A child theme's `theme.json` can point to them
  with `file:./assets/fonts/…`, so renaming a file removes that font without an error.

---

## SCSS structure

```
assets/scss/
├── abstracts/     # Variables, mixins
├── base/          # Reset, root (CSS custom properties), typography, accessibility
├── layout/        # Grid/containers, header, footer, content
├── components/    # Links, buttons, navigation, icons, card
└── gutenberg/     # Block-specific styles (alignments, table, etc.)

assets/scss/editor-style.scss   # Block editor only (see Block editor below)
```

---

## Typography & line length

Running text (paragraphs, list items, blockquotes, figcaptions) is capped at a readable
line length set in one place, `assets/scss/base/_root.scss`:

```scss
--measure: 42rem; // ~72 characters per line in Montserrat
```

Aim for **66–75 characters per line** (WCAG 1.4.8 recommends at most 80). The right
value depends on the font, so adjust it per site and count the characters on a full
line. Narrow fonts (e.g. Inter, Roboto) need a lower value, around 36–38rem.
`ch` is deliberately not used: it is the width of "0", which in wide fonts like
Montserrat gives far more than 70 characters at 70ch.

`.ingress` has its own, shorter width (`45rem`) because of its larger font size —
update it together with `--measure`.

Paragraphs that are centered, right-aligned or have a background keep the full width,
so the alignment and background apply across the content area.

Body text uses `hyphens: manual`: automatic hyphenation makes text harder to read,
e.g. for people with dyslexia. Editors can add soft hyphens (`&shy;`) where needed.

The body text size (`medium`) is set on `body` only, so paragraphs and list items
inherit it: a component that sets a smaller size on its container (a top bar, a card,
the footer) gets it on the text inside too.

---

## Colors

Colors are defined once, in the `theme.json` palette. WordPress outputs them as
`--wp--preset--color--{slug}`, and `assets/scss/base/_root.scss` maps them to the
theme's own variables (`--color-primary`, `--color-gray-500` etc.). The SCSS variables
(`$color-primary`) are shorthands for those CSS variables.

This means colors are resolved in the browser, not at build time: a child theme changes
a color in its own `theme.json` and links, buttons, focus rings and the editor's color
picker follow — without rebuilding the parent's CSS.

Sass cannot compute with CSS variables, so `color.adjust()` and `rgba($color, …)` do not
work on them. Use `color-mix()` instead, e.g. a darker hover:

```scss
background-color: color-mix(in srgb, #{$color-primary}, #000 15%);
```

## Design tokens

The parent theme is the structure; the design belongs to the child theme. Every design
value is a token in `theme.json`, read by a CSS variable in `base/_root.scss` (with the
same value as fallback), so a child theme changes the design in its own `theme.json` —
merged with the parent's — without rewriting CSS rules.

| `theme.json` | CSS variable | What |
|------|------|------|
| `color.palette` | `--color-*` | Colors (see [Colors](#colors)) |
| `custom.color.background` / `text` / `heading` | `--background-color`, `--text-color`, `--heading-color` | Page background, body text and headings (headings inherit the text color by default) |
| `custom.link.hoverColor` | `--link-hover-color` | Link hover color |
| `typography.fontFamilies` (`body`, `heading`) | `--font-family-body`, `--font-family-heading` | Fonts (see [Fonts](#fonts)) |
| `typography.fontSizes` | `--wp--preset--font-size--*` | Font sizes, `h-1` … `h-6`, `display-1`, `large`, `small` … |
| `spacing.spacingSizes` (`xs` … `xl`) | `--space-xs` … `--space-xl` | Spacing scale, also in the editor |
| `layout.contentSize` | `--container-max-width` | Content width |
| `shadow.presets` (`sm` … `xl`) | `--shadow-sm` … `--shadow-xl` | Shadows, also in the editor |
| `custom.measure` | `--measure` | Line length of running text |
| `custom.lead.width` / `lineHeight` | `--lead-width`, `--lead-line-height` | `.ingress` |
| `custom.fontWeight.regular` … `bold` | `--font-weight-*` | Font weights |
| `custom.lineHeight.body` / `heading` | `--line-height-body`, `--line-height-heading` | Line heights |
| `custom.display.lineHeight` / `letterSpacing` / `textTransform` / `color` | `--display-*` | `.display-1` (transform and color default to the headings') |
| `custom.heading.textTransform` | `--heading-text-transform` | All headings, e.g. `uppercase` |
| `custom.heading.h1` … `h6`: `fontWeight`, `lineHeight`, `letterSpacing`, `textTransform`, `color` | `--h1-font-weight` … `--h6-color` | Headings per level |
| `custom.radius.sm` / `base` / `md` / `lg` / `pill` | `--radius-*` | Corner radius |
| `custom.button.radius` / `padding` / `gap`, `sm` and `lg`: `padding` / `fontSize` | `--btn-*` | Button shape and sizes |
| `custom.button.primaryHoverBackground` / `successHoverBackground` / `dangerHoverBackground` | `--btn-*-hover-bg` | Button hover colors |
| `custom.focus.width` / `offset` | `--focus-width`, `--focus-offset` | Focus outline |
| `custom.header.background` / `borderBottom` / `paddingBlock` / `marginBottom` | `--header-*` | Site header |
| `custom.nav.gap` / `itemPadding` / `hoverBackground` / `submenuWidth` / `submenuShadow` / `overlay` / `panelBackground` | `--nav-*` | Main menu: layout, the mobile panel and submenus |
| `custom.nav.fontSize` / `fontWeight` / `textTransform` / `letterSpacing` / `color` / `hoverColor` / `activeColor` / `activeFontWeight` / `activeIndicator` | `--nav-*` | Main menu links. Unset = inherited from the page. `activeIndicator` is a `box-shadow`, e.g. `inset 0 -2px 0 currentcolor` for a line |
| `custom.logo.height` / `color` | `--logo-height`, `--logo-color` | Logo (the SVG logo and the text fallback) |
| `custom.logo.text.fontFamily` / `fontSize` / `fontWeight` / `letterSpacing` / `textTransform` | `--logo-*` | The site name when there is no logo |
| `custom.footer.background` / `color` / `linkColor` / `paddingBlock` | `--footer-*` | Site footer |
| `custom.footer.fontSize` | `--footer-font-size` | Footer copyright row |
| `custom.transition.base` | `--transition-base` | Transitions |

The hover colors default to a darker shade of the palette color (`color-mix()`), so
they follow a new palette without being set.

The SCSS abstracts have shorthands for the common ones (`$space-xs` … `$space-xl`,
`$radius-*`, `$font-weight-*`, `$line-height-*`, `$transition-base`). Sass cannot compute with them — use `calc()`.

**Not tokens:** the breakpoints (`$breakpoints`) — CSS variables do not work in media
queries — and pure mechanics such as the menu icon's lines.

Keys with a digit become two words in the CSS variable: `heading.h1.lineHeight` is
`--wp--custom--heading--h-1--line-height`.

Example — a child theme with lighter body text, uppercase headings (except h5), nearly square buttons and a wider page:

```json
{
  "settings": {
    "layout": { "contentSize": "90rem" },
    "custom": {
      "color": { "text": "var(--wp--preset--color--gray-600)", "heading": "var(--wp--preset--color--gray-900)" },
      "radius": { "base": "0" },
      "button": { "radius": "2px", "padding": "0.75rem 1.75rem" },
      "heading": {
        "textTransform": "uppercase",
        "h1": { "lineHeight": "0.95", "letterSpacing": "0.01em" },
        "h2": { "lineHeight": "1" },
        "h5": { "textTransform": "none" }
      }
    }
  }
}
```

Only the keys you set are changed; the rest come from the parent. `spacingSizes`,
`fontSizes` and `shadow.presets` are lists: a child theme's list replaces the parent's,
so include every slug (`xs` … `xl` etc.).

---

## Header, menu and footer

**Widget areas** (blocks can be used in them): **Sidebar** (`sidebar-1`) and **Footer
one … four** (`footer-1` … `footer-4`). The header has none; a child theme that needs
something next to the menu overrides `template-parts/header/branding.php` or
`header.php`.

**Logo and site name:** `template-parts/header/branding.php` picks the logo —
`assets/svg/logo.svg`, else the uploaded logo, else the site title (Settings → General) as text
(`template-parts/components/site-title.php`), as in a regular WordPress theme. A child
theme copies `branding.php` to build its own header branding (e.g. the site name with the
tagline, or a different logo on mobile) without copying `header.php`.

**Footer:** the widget areas take blocks (columns, headings, images, ACF blocks …), so the
footer is built with blocks in **Appearance → Widgets**. For a different layout, copy
`footer.php` to the child theme and change it — keep `wp_footer()` and
`do_action( 'wpst_footer_copyright_end' )` (the cookie link is printed there). Register
other widget areas in the child theme with `register_sidebar()`.

---

## Fonts

Fonts are self-hosted in `assets/fonts/` as variable woff2 files (latin subset, all
weights in one file) and registered with `fontFace` in `theme.json`. WordPress outputs
the `@font-face` rules on the front end and in the block editor. Nothing is loaded from
Google, so no visitor IP addresses are sent to a third party (GDPR).

| Font | File | Source |
|------|------|--------|
| Montserrat (body) | `montserrat-latin-wght-normal.woff2` | [Fontsource](https://fontsource.org/fonts/montserrat) |
| Fraunces (headings) | `fraunces-latin-opsz-normal.woff2` | [Fontsource](https://fontsource.org/fonts/fraunces) |

Both are licensed under the SIL Open Font License (`assets/fonts/OFL-*.txt`).

**Other fonts in a child theme:** put the woff2 files in the child theme's
`assets/fonts/` and define `fontFamilies` with `fontFace` in the child's `theme.json`.
The families with the slugs `body` and `heading` are the site's fonts — the theme's CSS
uses them through `--font-family-body` and `--font-family-heading`, so no CSS changes
are needed. Latin covers Swedish (å, ä, ö); use the
`latin-ext` files if a site needs e.g. Polish or Czech characters.

**Google Fonts anyway:** return a URL from the `wpst_google_fonts_url` filter.

---

## Block editor

`assets/scss/editor-style.scss` makes the editor canvas match the front-end `.container`:
same side padding, same content width and left-aligned blocks with the same `--measure`.

WordPress scopes editor styles with `:where(.editor-styles-wrapper)`, which adds no
specificity, so the theme's front-end rules lose against WordPress's own layout rules
for the root block list. The editor rules therefore target
`.block-editor-block-list__layout.is-root-container` directly.

---

## Privacy & cookie policy pages

On theme activation, `inc/privacy.php` creates the privacy policy and cookie policy
pages as drafts with Swedish GDPR templates. Replace all bracketed placeholders (`[FÖRETAGSNAMN]`, `[E-POST]`, `[DATUM]` etc.) before publishing.

- An existing page is filled only once, and only while it is still a draft
  (e.g. the empty page WordPress creates on install). Published or edited pages are
  never overwritten when the theme is reactivated.
- The pages link to each other with the placeholders `#wpst-privacy-policy` and
  `#wpst-cookie-policy`. They are replaced with the pages' current URLs on output,
  so leave them as they are. A link to an unpublished page becomes plain text.

---

## Translations

The theme text domain is `wp-starter-theme` and the Swedish translation lives in
`languages/`. After adding or changing strings, run in this repo:

```bash
composer i18n      # update the .pot and merge new strings into sv_SE.po
# Translate the new strings in sv_SE.po, then:
composer i18n:mo   # compile sv_SE.mo
```

Check that no translations are marked `#, fuzzy` — fuzzy entries are left out of the
`.mo` file and show up in English. Theme.json labels (font sizes, colors, etc.) are
translated through the same files.

---

## Buttons & Links

All CSS is located in:
- `assets/scss/components/_links.scss` — all link styles
- `assets/scss/components/_buttons.scss` — all button styles
- PHP helper: `inc/helpers-buttons.php` → `wpst_button()`
- SVG icons: `assets/svg/icon-intern-link.svg`, `icon-extern-link.svg`

---

### Links

#### Standard `<a>` — neutral (no class)
All `<a>` outside `.btn` are neutral: color + hover color, no underline.

```html
<a href="/about">About us</a>
```

#### `.link-animated` — animated underline
Opt-in animated underline that slides in on hover.

```html
<a href="/about" class="link-animated">About us</a>
```

#### `.link-icon` — link with icon
Opt-in icon after the link text. SVG is inlined via `wpst_icon()` which automatically determines internal/external icon based on the URL.

```php
// Internal link (arrow icon)
<a href="/about" class="link-icon">
    About us <?php wpst_icon( 'icon-intern-link' ); ?>
</a>

// External link (external icon)
<a href="https://example.com" class="link-icon" target="_blank" rel="noopener noreferrer">
    External site <?php wpst_icon( 'icon-extern-link' ); ?>
</a>
```

---

### Buttons

The base class is `.btn`. Always add a variant. The PHP helper `wpst_button()` is recommended.

#### Variants

| Class | Usage |
|-------|-------|
| `.btn-primary` | Primary CTA (default) |
| `.btn-secondary` | Secondary action |
| `.btn-light` | Light background |
| `.btn-dark` | Dark background |
| `.btn-ghost` | Outline button |
| `.btn-success` | Confirmations / success actions |
| `.btn-danger` | Destructive / delete actions |

#### Sizes

| Class | Usage |
|-------|-------|
| *(none)* | Default size |
| `.btn-sm` | Small |
| `.btn-lg` | Large |

#### Plain HTML

```html
<a href="/contact" class="btn btn-primary">Contact us</a>
<button type="submit" class="btn btn-primary">Submit</button>
<a href="/about" class="btn btn-ghost btn-lg">Read more</a>
```

#### PHP helper `wpst_button()`

```php
// Simple button
wpst_button( array(
    'label' => 'Contact us',
    'href'  => '/contact',
) );

// Button with icon (automatic internal/external detection)
$is_external = ! empty( $link_host ) && $link_host !== $home_host;
$icon_name   = $is_external ? 'icon-extern-link' : 'icon-intern-link';

wpst_button( array(
    'label'     => 'Visit page',
    'href'      => $url,
    'external'  => $is_external,
    'icon_html' => wpst_icon( $icon_name, array( 'echo' => false ) ),
    'icon_pos'  => 'after',
) );

// Form button
wpst_button( array(
    'label' => 'Submit',
    'type'  => 'submit',
) );
```

#### Arguments for `wpst_button()`

| Argument | Default | Description |
|----------|---------|-------------|
| `label` | `''` | Visible text |
| `variant` | `'primary'` | Button variant (see table above) |
| `size` | `''` | `btn-sm` or `btn-lg` |
| `href` | `''` | URL — renders `<a>`, otherwise `<button>` |
| `type` | `'button'` | `button`, `submit`, `reset` |
| `target` | `''` | `_blank` etc. |
| `external` | `false` | Adds `rel="noopener noreferrer"` |
| `disabled` | `false` | Disables the button |
| `icon_html` | `''` | SVG string (e.g. from `wpst_icon()`) |
| `icon_pos` | `'before'` | `before` or `after` |
| `class` | `array()` | Extra CSS classes |
| `aria_label` | `''` | Accessible label |
| `display` | `true` | `false` returns HTML instead of echoing |

---

### SVG icons

```php
wpst_icon( 'icon-intern-link' );                                   // Echo directly
$svg = wpst_icon( 'icon-intern-link', array( 'echo' => false ) );  // Return as string
wpst_icon( 'icon-intern-link', array( 'title' => 'Go to' ) );      // With a11y title
```

| File | Usage |
|------|-------|
| `icon-intern-link.svg` | Arrow — internal links |
| `icon-extern-link.svg` | External icon — external links |

---

## Contact details (Swedish formats)

`inc/helpers-format.php` has helpers that tidy contact details however they were typed.
They have no output and do nothing on their own: call them on a value, or hook them to
a field so the value is saved in the standard format. Foreign numbers are left as typed.
Each is wrapped in `function_exists()`, so a child theme can replace it.

| Function | Example |
|------|-------|
| `wpst_format_phone( $phone )` | `+46 70 123 45 67` → `070-123 45 67`, `08 12345678` → `08-123 456 78` |
| `wpst_phone_href( $phone )` | `070-123 45 67` → `tel:+46701234567` (empty string without a number) |
| `wpst_format_postcode( $postcode )` | `85234` → `852 34` |
| `wpst_normalize_email( $email )` | ` Info@Firma.SE ` → `info@firma.se` |

```php
// Child theme: save the ACF fields in the standard format.
add_filter( 'acf/update_value/key=field_contact_phone', 'wpst_format_phone' );
add_filter( 'acf/update_value/key=field_contact_postcode', 'wpst_format_postcode' );
add_filter( 'acf/update_value/key=field_contact_email', 'wpst_normalize_email' );
```

```php
<a href="<?php echo esc_url( wpst_phone_href( $phone ) ); ?>"><?php echo esc_html( wpst_format_phone( $phone ) ); ?></a>
```
