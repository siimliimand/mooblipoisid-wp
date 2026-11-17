# Multilingual Content Model — Proposal

## Why

The site currently serves content in Estonian only, but Mööblipoisid operates across Tallinn, Stockholm and Helsinki and needs the site to reach Estonian-, Russian- and English-speaking clients. The `multilingual-content` spec is entirely unmet. The recent content-model restructure (CPTs + taxonomies + Homepage Settings) deliberately made the content structured and admin-editable specifically so a multilingual plugin can translate real posts/terms rather than opaque homepage markup — this change completes that plan.

## What Changes

- Introduce a multilingual layer using **Polylang (free)** with three languages: **Estonian (`et`, default)**, **Russian (`ru`)** and **English (`en`)**, served via subdirectory URLs (`/`, `/ru/`, `/en/`).
- Add a **language switcher** to the site (header + footer) and emit **`hreflang`** / language-alternate annotations so each language version is independently crawlable and discoverable.
- Mark the existing content model as **translatable**: the six custom post types (`project`, `testimonial`, `range`, `capability`, `step`, `trust_stat`) and two taxonomies (`sector`, `project_tag`) become language-aware, each with per-language post/term translations.
- Make the **Homepage Settings** option (`mp_homepage_settings`) **per-language** (hero, ethos, consultation intro text) so the static one-off text is also translatable.
- Localise the **navigation** (primary / mobile / footer menus) and internal links per language so browsing stays within one language.
- Use **Loco Translate** to translate the theme's gettext strings (enquiry form labels, placeholders, validation/success messages, CPT/taxonomy UI labels) into `ru` and `en`.
- Translate **visitor-facing content** into `ru` and `en`: pages (Home, legal pages), project/testimonial/range copy, footer legal info, and the enquiry feedback message. Translation is machine-assisted then human-edited.
- Per-language SEO: distinct crawlable URLs, page titles and meta descriptions per language, plus `hreflang` alternates.

## Capabilities

### New Capabilities

_(none — the multilingual behaviour is already specified under the existing `multilingual-content` capability.)_

### Modified Capabilities

- `multilingual-content`: Requirements become concretely implementable and are now backed by a specific tool choice (Polylang + Loco Translate), per-language content model, per-language Homepage Settings, localised navigation and `hreflang`. Add scenarios covering the language switcher, per-language CPT/taxonomy content, per-language homepage settings, and untranslated-string detection.
- `editable-content-model`: Homepage Settings (`mp_homepage_settings`) and the CPT/taxonomy content become **language-scoped** rather than single-language — editors manage a translation per language. Requirements adjust to describe per-language editing and the language switcher in the editor UI.
- `front-end-design`: The rendered homepage now resolves content by the active language; the language switcher becomes a new design element in the header/footer that must match the design system (though the base design HTML is Estonian-only).

## Impact

- **Plugins**: add `polylang` (free) and `loco-translate`; no premium plugins required.
- **Content model** (`inc/content-model.php`): register CPTs/taxonomies as translatable; make `mp_homepage_settings` language-aware (per-language option storage) and update the `mooblipoisid_get_homepage_settings()` helper to resolve the current language.
- **Templates** (`front-page.php`, `template-parts/home/*`): query posts/terms/settings by active language; add language switcher markup in header/footer.
- **functions.php**: wrap remaining hardcoded strings in gettext for Loco; ensure form labels/messages are translatable.
- **Menus** (`primary`, `mobile`, `footer`): create per-language menu instances and link translations.
- **SEO**: `hreflang` annotations (Polylang outputs these), per-language titles/meta.
- **Content**: real translated copy needed for all pages and model content in `ru`/`en` (manual copy work — out of scope for the technical build, tracked as a follow-up content task).
- **Scope note**: building the multilingual *machinery* is in scope; producing polished human translations of all copy is a parallel content effort and may lag the technical implementation.