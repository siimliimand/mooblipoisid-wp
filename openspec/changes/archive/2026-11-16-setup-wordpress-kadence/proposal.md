## Why

The repo currently contains only docs (a static HTML design prototype and specs), but no running site. We need the actual WordPress foundation in place so that content work, trilingual setup and further development have something to build on. The prototype in `docs/html/index.html` defines the approved visual direction, so the theme must be configured to match it rather than shipped with Kadence defaults.

## What Changes

- Add a DDEV (Docker) based local WordPress environment; the WordPress docroot lives in `public/` and is committed to the repo (a plain `docker-compose.yml` alternative is included for environments without DDEV)
- Install WordPress core and configure the site identity for Mööblipoisid OÜ (Estonian as the site language)
- Install and activate the Kadence theme, plus a Kadence child theme (`mooblipoisid-child`) for all project custom code
- Configure Kadence globals to match the prototype: warm brown/sand color palette, Playfair Display (headings) + Plus Jakarta Sans (body) typography, spacing and button styles
- Build the homepage in Kadence so its structure mirrors the prototype: header with logo + nav + "Request a Quote" CTA, hero, trust strip, sectors (hospitality / retail / private), product ranges, featured projects, capabilities, process, ethos band, testimonials, consultation/contact section with enquiry form, footer with legal data
- Use real Mööblipoisid content (names, contacts, legal data) everywhere; prototype placeholder images are kept only as temporary placeholders pending real photography
- No multilingual plugin work in this change — Estonian-only setup; `ru`/`en` come in a follow-up change

## Capabilities

### New Capabilities

- `front-end-design`: The visual design system and homepage presentation delivered through Kadence — color palette, typography, header/footer structure, and homepage sections matching `docs/html/index.html`

### Modified Capabilities

<!-- none — this change implements existing site-platform and brand-identity requirements; no requirement-level changes -->

## Impact

- **Repository layout**: new `public/` docroot (WordPress core + child theme), `.ddev/config.yaml`, `docker-compose.yml`
- **Dependencies**: WordPress core, Kadence theme (both committed or pinned for reproducibility)
- **Systems**: local DDEV stack; nothing touches zone.ee production in this change
- **Follow-up work enabled**: multilingual plugin setup, real photography, enquiry form delivery configuration
