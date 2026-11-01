# WP Starter Theme

A modern WordPress starter theme built with SCSS (ITCSS), Gulp, and Bedrock.
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
- Dashboard widget for recommended plugins with composer.json integration
- Auto-generated privacy policy and cookie policy pages on theme activation
- i18n-ready with Swedish (sv_SE) translation included

---

## Installation

This theme is part of a Bedrock project and is managed via Composer.
See the project root `README.md` for full setup instructions.

To activate, log in to WordPress admin and go to **Appearance › Themes**.

---

## Development

Run from inside `web/app/themes/wp-starter-theme/`:

```bash
npm install        # Install dependencies
npm run dev        # Watch + BrowserSync
npm run build      # Production build
npm run lint       # Lint JS, CSS and PHP
npm run fix        # Auto-fix JS, CSS and PHP
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
3. Commit, tag and push as the script prints (`git tag v1.2.0`). Composer reads the
   git tags as versions.

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
`assets/fonts/` and define `fontFamilies` with `fontFace` in the child's `theme.json`,
then update `font-family` in the child's CSS. Latin covers Swedish (å, ä, ö); use the
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
`languages/`. After adding or changing strings, run from the project root:

```bash
cd web/app/themes/wp-starter-theme
../../../../vendor/bin/wp i18n make-pot . languages/wp-starter-theme.pot --domain=wp-starter-theme --exclude=node_modules,assets
../../../../vendor/bin/wp i18n update-po languages/wp-starter-theme.pot languages/sv_SE.po
# Translate the new strings in sv_SE.po, then:
../../../../vendor/bin/wp i18n make-mo languages/sv_SE.po languages/
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
