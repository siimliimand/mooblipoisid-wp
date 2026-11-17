# Multilingual Content Model — Design

## Context

The site is a WordPress + Kadence child-theme build on DDEV, currently Estonian-only. The homepage is rendered dynamically by `front-page.php` + `template-parts/home/*` from a structured content model registered in `inc/content-model.php`: six custom post types (`project`, `testimonial`, `range`, `capability`, `step`, `trust_stat`), two taxonomies (`sector`, `project_tag`), and a single `mp_homepage_settings` option for the one-off hero/ethos/consultation text. All post types/taxonomies are registered `public` + `show_ui` + `show_in_rest` specifically so a multilingual plugin can recognise and translate them.

The business operates across Tallinn, Stockholm and Helsinki and needs `et` (default) / `ru` / `en`. The `multilingual-content` spec requires per-language URLs, full translation coverage, localised navigation and per-language SEO with `hreflang`.

Menus: `primary`, `mobile`, `footer` (Kadence locations). The enquiry form is a `[mooblipoisid_enquiry_form]` shortcode with gettext-wrapped labels in `functions.php`.

## Goals / Non-Goals

**Goals:**
- Serve the site in `et` (default), `ru`, `en` via subdirectory URLs (`/`, `/ru/`, `/en/`).
- Make all content model entities (CPTs, taxonomies, Homepage Settings) and all pages/menus per-language.
- Language switcher in header + footer; `hreflang` / alternate annotations per page.
- Localised navigation and internal links (never cross languages).
- Translate theme gettext strings (enquiry form, admin labels) via Loco Translate.
- Per-language SEO: crawlable URLs, titles, meta.
- Keep `et` as the source/default so existing content and URLs stay stable.

**Non-Goals:**
- Producing polished, human-edited `ru`/`en` copy for every page and card (parallel content effort; machine-assisted drafts may land first).
- WPML or any premium multilingual plugin (Polylang free is sufficient).
- Automatic/real-time machine translation at request time (translations are stored, not computed).
- Right-to-left languages (all three target languages are LTR).
- Changing the visual design beyond adding the language switcher.

## Decisions

**D1 — Polylang (free) as the multilingual engine.**
- *Why:* Mature, free, works with custom post types + taxonomies out of the box, stores translations as real posts/terms (matches our content-model strategy), outputs `hreflang` automatically, provides per-language menus and a language switcher widget/block. No license cost.
- *Alternatives:* WPML (paid, heavier), qTranslate X (abandoned, string-based — bad fit for structured content), manual per-language pages (unmaintainable). Polylang free wins on cost + structured-content fit.

**D2 — Subdirectory URL structure: `/` (et) / `/ru/` / `/en/`.**
- *Why:* Clean, crawlable, matches the spec's example. Estonian keeps the root so existing URLs/SEO don't break.
- *Alternatives:* Subdomains (`ru.mooblipoisid.ee` — more DNS/TLS setup), query `?lang=` (poor SEO). Subdirectories are simplest and best for SEO.

**D3 — Store translations as separate posts/terms linked by Polylang, not string arrays.**
- *Why:* Editors manage a real translation post per language (native Polylang UX); the content model queries resolve the active language automatically.
- *Alternatives:* Storing all languages in one post's meta (custom, hard to maintain, no plugin support). Rejected.

**D4 — Make `mp_homepage_settings` language-aware.**
- The single option becomes a per-language option set (e.g. `mp_homepage_settings` keyed by language code, or Polylang's option-translation mechanism if leveraged). The `mooblipoisid_get_homepage_settings()` helper resolves the current language and falls back to `et` for missing keys.
- *Why:* Hero/ethos/consultation text must be translatable like everything else.
- *Alternatives:* Turn each hero field into a CPT/option per language (overkill). A per-language option keyed by lang code keeps the existing admin screen and helper shape, minimising template changes.

**D5 — Loco Translate for gettext strings.**
- *Why:* The enquiry form labels, validation/success messages and admin UI labels are gettext strings in the child theme's `mooblipoisid-child` text domain. Loco edits/creates the `ru`/`en` `.po`/`.mo` files without touching code. Polylang handles content; Loco handles UI strings.
- *Alternatives:* Hand-editing `.po` files (locally viable but Loco gives a non-technical editor a UI). 

**D6 — Wrap any remaining hardcoded front-end strings in gettext.**
- Ensure every visitor-facing string in `functions.php` / templates uses `__()` / `esc_html_e()` with the `mooblipoisid-child` domain so Loco can find it. CPT/taxonomy registration labels likewise.

**D7 — Per-language menus.**
- Polylang creates a menu per language for the `primary`, `mobile`, `footer` locations. Internal links point to the same-language page. The switcher lets visitors jump between languages.

**D8 — `hreflang` via Polylang.**
- Polylang emits `hreflang`/`alternate` link annotations and per-language `<title>`/meta. No custom SEO plugin required for this scope.

## Risks / Trade-offs

- **[Content volume] → Translation is manual/large.** Machine-translate-then-edit to seed `ru`/`en`; track untranslated strings and fill over time. `et` remains complete and default, so an untranslated `ru`/`en` page falls back gracefully while being flagged.
- **[Per-language Homepage Settings complexity] → Keep the admin screen shape.** Store a language-keyed option; keep the same fields and helper signature so templates change minimally. Fallback to `et` for any missing language key.
- **[Polylang + Kadence interaction] → Verify the theme's menu/header/footer and `front-page.php` resolve language correctly.** Front page must be a static page set per language; test that `front-page.php` still drives rendering in each language.
- **[URL/SEO migration] → `et` keeps the root path** so existing URLs don't change; only new `/ru/` `/en/` prefixes are added. Rollback = deactivate Polylang (content stays as posts).
- **[Untranslated-string leakage] → Add a check/QA step** that loads each `ru`/`en` page and asserts no Estonian-only visitor-facing strings remain (per the spec scenario). Use Loco's missing-string report.

## Migration Plan

1. Install + activate Polylang; add `et` (default), `ru`, `en`; set subdirectory URL mode.
2. Enable the 6 CPTs + `sector`/`project_tag` as translatable in Polylang settings.
3. Make `mp_homepage_settings` language-aware; update `mooblipoisid_get_homepage_settings()` + templates.
4. Create per-language menus for `primary`/`mobile`/`footer`.
5. Install Loco Translate; create `ru`/`en` translations for `mooblipoisid-child` strings.
6. Add language switcher to header/footer; confirm `hreflang` output.
7. Seed `ru`/`en` translations for pages + model content (machine-assisted, human-edited).
8. QA each language: URL, switcher, navigation stays in-language, no untranslated leakage, per-language SEO.

**Rollback:** Deactivate Polylang → site reverts to `et`-only; content posts/terms remain intact (translations are just additional posts).

## Open Questions

- Exact Polylang mechanism for translating the `mp_homepage_settings` option (language-keyed option vs. Polylang's option translation) — decide during implementation, favouring the least-invasive to the existing admin screen.
- How to seed translations efficiently (WP-CLI export/import vs. manual entry) — a follow-up content task.
- Whether the language switcher is a Kadence element, a Polylang widget/block, or custom markup in the child theme header/footer — choose whichever matches the design system cleanly.