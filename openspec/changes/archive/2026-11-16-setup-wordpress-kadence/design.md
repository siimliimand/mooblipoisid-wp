## Context

The repo has design docs only (`docs/html/index.html` prototype, `README.md`, OpenSpec specs). No WordPress code exists yet. Production target is zone.ee shared PHP hosting (Apache, MySQL, PHP 8.x); local development is DDEV-first with a plain Docker fallback. The prototype is a static Tailwind-CDN page — it defines the visual direction but its CSS approach is not to be copied into WordPress; Kadence provides the design system instead.

## Goals / Non-Goals

**Goals:**

- Reproducible local WordPress environment (DDEV primary, docker-compose fallback)
- Kadence + child theme installed and activated; all custom code in the child theme
- Kadence global colors/typography configured to match the prototype
- Homepage rebuilt with WordPress content (native blocks + Kadence blocks) mirroring the prototype's eleven sections
- Real Mööblipoisid content everywhere; no prototype placeholder fiction

**Non-Goals:**

- Multilingual plugin and `ru`/`en` content (follow-up change)
- Production deployment to zone.ee (follow-up change)
- Real photography (placeholder images allowed with tracking)
- Finished enquiry-form email delivery (form embedded now; SMTP/delivery hardened in a follow-up)

## Decisions

1. **WordPress lives in `public/`, committed to git** (core + Kadence + child theme). Rationale: simplest path to zone.ee SFTP deployment and reproducible local setups; the site is small and upgrade-controlled. Alternative considered: Composer-managed WP (Bedrock-style) — rejected for now as unnecessary complexity for a single-site brochure site.
   - `wp-config.php` is gitignored (environment-specific); `wp-config-sample.php` is committed. `wp-content/uploads/` is gitignored.

2. **Child theme owns design tokens in code.** `mooblipoisid-child/style.css` defines CSS custom properties for the palette and enqueues fonts (Playfair Display, Plus Jakarta Sans) via Kadence's typography settings. Rationale: Kadence customizer settings live in the DB and are hard to review in git; keeping the palette also in code gives a reviewable source of truth and survives database loss. Kadence customizer is still set to the same values so core blocks and Kadence blocks inherit the tokens.

3. **Homepage is a normal WordPress page built with Gutenberg + Kadence Blocks** (free Kadence Blocks plugin for row/layout, advanced buttons, icons). No page builder like Elementor — keeps markup lean and stays within the Kadence ecosystem. Sections are anchor-linked (`#hospitality`, `#retail`, `#projects`, `#capabilities`, `#process`, `#maker`, `#consultation`) to support the header nav.

4. **Enquiry form uses the Kadence Blocks form block** (name, company, email, phone, project type, timeline, location, brief) sending to both manager addresses. Rationale: no extra plugin dependency in this change. Email deliverability on shared hosting (SPF/DKIM, SMTP plugin) is deferred to the lead-capture follow-up; noted as risk.

5. **PHP 8.3 / MariaDB 10.11 locally** in DDEV (`--project-type=wordpress`, docroot `public`), with a matching simple `docker-compose.yml` (wordpress:php8.3-apache + mariadb) for non-DDEV environments. PHP version must be re-checked against the bought zone.ee plan before production deploy.

6. **Estonian-only install** (`WPLANG=et_ET`) in this change; URL structure decisions for `/ru/`, `/en/` are made when the multilingual plugin is chosen, so homepage slugs stay simple (`/`).

## Risks / Trade-offs

- [Full WP core in git bloats the repo and risks drift with updates] → pin the exact WP version installed; document `wp core update` + commit flow in README before production
- [Kadence customizer settings are DB-bound and not portable in git] → child theme CSS variables duplicate the tokens; export a `.dat` customizer backup into `docs/` after configuration
- [Kadence Blocks form email delivery on shared hosting may land in spam or fail] → submissions are the site's core conversion; harden with SMTP plugin + SPF/DKIM in the lead-capture follow-up, and verify delivery immediately after this change
- [Prototype fidelity vs Kadence defaults] → some prototype styling (Tailwind utility classes) has no 1:1 Kadence equivalent; accept near-match fidelity, tracked against the front-end-design spec's side-by-side scenario
- [zone.ee PHP version may differ from local 8.3] → verify plan settings before production migration; DDEV PHP version is a one-line change

## Migration Plan

Local-first: `ddev start` → install WP → activate themes → build homepage. Rollback is trivial (delete environment, `git checkout`). Production migration happens in the follow-up deployment change (SFTP upload + DB import + wp-config with zone credentials).

## Open Questions

- Domain name for production (needed before zone.ee deploy change, not this one)
- Kadence Blocks free vs Pro — Pro adds form entry storage; decide when hardening lead capture
- Final photography sources and licensing
