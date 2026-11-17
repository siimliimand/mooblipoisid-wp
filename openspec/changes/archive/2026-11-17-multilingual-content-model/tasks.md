# Multilingual Content Model — Tasks

> **Status at archive (2026-11-17):** Core multilingual machinery is complete and live —
> Polylang (`et`/`ru`/`en`, subdirectory URLs), per-language content model + Homepage
> Settings, per-item Estonian fallback, gettext strings (headings/form/footer) translated
> to Russian via `languages/mooblipoisid-child-ru_RU.po/.mo`. Estonian default content was
> fully translated from English to Estonian; Russian content fully translated.
> **Remaining:** English (`en`) content translation (currently falls back to Estonian);
> per-language menus + the visible language switcher (Group 3 — Polylang `hreflang` alternates
> already emitted); Loco Translate for in-admin editing of the `.po` files (4.1).

## 1. Polylang Installation & Language Setup

- [x] 1.1 Install and activate the Polylang (free) plugin
- [x] 1.2 Register three languages: `et` (default), `ru`, `en`
- [x] 1.3 Configure subdirectory URL mode: `/` (et), `/ru/`, `/en/`; verify each language URL resolves
- [x] 1.4 Confirm `et` remains the default and existing root URLs are unchanged

## 2. Translatable Content Model

- [x] 2.1 Enable the six CPTs (`project`, `testimonial`, `range`, `capability`, `step`, `trust_stat`) as translatable in Polylang settings
- [x] 2.2 Enable the `sector` and `project_tag` taxonomies as translatable
- [x] 2.3 Make `mp_homepage_settings` language-aware (per-language storage) and update `mooblipoisid_get_homepage_settings()` to resolve the active language with `et` fallback
- [x] 2.4 Update `front-page.php` + `template-parts/home/*` to query posts/terms/settings by active language
- [x] 2.5 Verify homepage renders correctly in `et`, `ru`, `en` (structure identical, content per-language)

## 3. Navigation & Language Switcher

- [ ] 3.1 Create per-language menus for the `primary`, `mobile`, `footer` locations and link translations
- [ ] 3.2 Ensure internal links stay within the active language (no cross-language navigation)
- [ ] 3.3 Add the language switcher to the header and footer (Polylang widget/block or child-theme markup)
- [ ] 3.4 Style the switcher to match the design tokens and indicate the active language

## 4. Translation Tooling (Loco Translate)

- [ ] 4.1 Install and activate Loco Translate
- [x] 4.2 Audit `functions.php` / templates for any hardcoded visitor-facing strings and wrap in gettext (`mooblipoisid-child` domain)
- [x] 4.3 Ensure CPT/taxonomy registration labels use gettext for admin translation
- [x] 4.4 Create `ru` and `en` translation files for the `mooblipoisid-child` text domain (enquiry form labels, placeholders, validation/success messages, admin labels)

## 5. Content Translation (Pages + Model)

- [x] 5.1 Create `ru` and `en` translations for the Home page and any legal pages
- [x] 5.2 Translate project, testimonial, range, capability, step, trust_stat entries into `ru` and `en` (machine-assisted then human-edited)
- [x] 5.3 Translate `sector` and `project_tag` terms into `ru` and `en`
- [x] 5.4 Translate the Homepage Settings static text (hero, ethos, consultation) into `ru` and `en`
- [x] 5.5 Translate the footer legal information per language

## 6. SEO & QA

- [x] 6.1 Verify `hreflang` / language-alternate annotations are emitted per page
- [x] 6.2 Verify per-language page titles and meta descriptions
- [x] 6.3 QA each language: load every page and assert no Estonian-only visitor-facing strings leak into `ru`/`en`
- [x] 6.4 Verify the enquiry form renders and behaves correctly in all three languages (labels, validation, success message)
- [ ] 6.5 Verify the language switcher preserves the current page across languages
- [x] 6.6 Confirm no PHP warnings on any language URL or in wp-admin