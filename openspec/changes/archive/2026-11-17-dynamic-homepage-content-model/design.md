# Dynamic Homepage Content Model — Design

## Context

The homepage is built from 10 raw `core/html` blocks inside Home page (post 5), ~23 KB of hand-written markup referencing CSS classes `mp-hero`, `mp-trust`, `mp-sector`, `mp-range`, `mp-project`, `mp-capability`, `mp-step`, `mp-quote` etc. All styling lives in the child theme `style.css` and works today. Nothing is editable by a non-technical person, and the content is opaque to translation tooling.

The site runs on WordPress + Kadence child theme in DDEV (PHP 8.3 / MariaDB). The enquiry form is already a shortcode (`[mooblipoisid_enquiry_form]`) rendered inside the `#consultation` block via a `render_block` filter; the footer is built from Kadence footer widgets. Both must keep working.

The near-term plan is to (1) make the repeatable sections dynamic and admin-editable, and (2) later add Estonian (default) / Russian / English translation with Polylang free + Loco Translate. This change does (1) only, structured so (2) drops in cleanly.

## Goals / Non-Goals

**Goals:**
- Move the repeatable homepage content (projects, testimonials, ranges, capabilities, process steps, trust stats, sectors) into structured, admin-editable WordPress entries.
- Move the static one-off text (hero, ethos, consultation intro) into an admin-editable options screen.
- Render the homepage from a `front-page.php` + template parts that reproduce the **exact current markup and CSS classes**, so the rendered page is pixel-identical and `style.css` is untouched.
- Provide a non-technical editing experience: clear labels, featured-image support, drag/field ordering, no HTML or code.
- Migrate existing content automatically so the site looks the same before and after.
- Register all new post types/taxonomies `public => true` so Polylang free will recognise them as translatable later.

**Non-Goals:**
- Adding translation / Polylang / Loco in this change (explicitly deferred to `multilingual-content`).
- Changing the visual design, layout, colors or typography.
- Changing the enquiry form behaviour, footer widgets, header or menus.
- Building detail/archive pages for the CPTs (they render as homepage cards only for now; `public => true` is for Polylang visibility, not standalone pages).
- Reworking how images are stored (uploads remain as-is).

## Decisions

### D1 — Content model: CPTs for rich cards, a taxonomy for sectors, options for static text
- **`project`** CPT: title, featured image, short description (excerpt), a "Location • Type" text field, and `project_tag` terms (material/feature tags). Card layout = current `.mp-project`.
- **`testimonial`** CPT: quote (title or content), author role, city, rating (1–5). Card = current `.mp-quote`.
- **`range`** CPT: title + description, ordered. Section 4 (product ranges).
- **`capability`** CPT: title + description, ordered. Section 6.
- **`step`** CPT: title + description + "Week xx – xx" text, ordered. Section 7 (process).
- **`trust_stat`** CPT: value (e.g. "18 Years") + caption. Section 2 (trust strip).
- **`sector`** taxonomy (on `project`): name, description, image, order. Section 3 (Sectors) + tags projects.
- **`project_tag`** taxonomy (on `project`): material/feature tags.
- **Homepage Settings** options screen: hero headline/sub/CTA labels + hrefs, ethos quote/body, consultation intro + manager contact lines.

*Rationale:* Items with their own identity + image + several fields → CPT (easy for a non-technical editor to add as a "Project"). Sectors are categories of work used to tag projects → taxonomy. One-off static prose → options. *Alternatives considered:* ACF Repeater fields for lists (rejected — harder to translate later with Polylang free and less discoverable); everything as options (rejected — poor for many repeatable items with images).

### D2 — Rendering via `front-page.php` + template parts, not shortcode soup
A `front-page.php` in the child theme includes one template part per section; each part runs a `WP_Query` / `get_terms()` and echoes the same HTML+classes as the current blocks. The static hero/ethos/consult parts read the Homepage Settings option.

*Rationale:* Keeps layout in exactly one place per section (translatable-ready), keeps content in the DB. *Alternative:* one shortcode per section (rejected — scattered markup and harder to keep sections in fixed order).

### D3 — Ordering via standard `page-attributes` / `menu_order`
Ranges, capabilities, steps, sectors and trust stats use `menu_order` and expose an "Order" field (plus optionally a lightweight drag-drop reorder plugin later). Projects order by a chosen date/menu_order with the lead card first.

*Rationale:* Simple, familiar, no custom code. Polylang syncs order across languages.

### D4 — Non-technical editing conventions
Use native WordPress building blocks wherever possible: Title, Excerpt ("short description"), Featured Image ("card image"), an Order box, and clearly-labelled meta boxes (or ACF if the user opts in) for the few extra strings (city, rating, week range, location•type). Register supports `title`, `editor`/`excerpt`, `thumbnail`, `page-attributes`. Admin labels written in plain language ("Short description shown under the card").

### D5 — One-time content migration script
A `wp eval` / CLI import parses the current `wp:html` blocks (source of truth kept at `docs/homepage-content.html`) and creates the posts/terms/options, attaching existing uploaded images by URL. After verification, the Home page's block markup is replaced with a single short placeholder (or emptied) so `front-page.php` drives rendering.

*Rationale:* Guarantees pixel-identical starting content and avoids manual re-entry.

### D6 — Keep form shortcode + footer widgets intact
The `#consultation` template part includes the `[mooblipoisid_enquiry_form]` token (still rendered via the existing `render_block`/shortcode path or directly via `do_shortcode()` in the template). Footer stays as Kadence widgets outside this change.

## Risks / Trade-offs

- [Pixel drift between old static markup and new template output] → Migration copies exact class names and structure; verification step diffs rendered HTML of old vs. new for each section.
- [CPTs get unwanted public single/archive URLs] → Set `public => true` but disable archives/hide from search where appropriate (`has_archive => false`, `exclude_from_search` as needed) while keeping `show_ui => true`; confirm Polylang still lists them (it keys off `public`/`show_ui`).
- [Non-technical editor confusion between "Project" and "Sector"] → Plain labels + a short editor README; sectors managed from the Projects screen as tags.
- [Ordering mistakes scramble homepage] → Sensible default `menu_order`, and template sorts stably (menu_order ASC, then date).
- [Re-running the import duplicates content] → Import is idempotent (guards on existing post meta marker) and only runs manually.
- [Options screen text left unlocalised later] → Static strings stored as plain values; Loco/Polylang string translation covers them in the later multilingual change.

## Migration Plan

1. Register CPTs/taxonomies + options screen (theme code) — no content change yet.
2. Run the one-time import into posts/terms/options (idempotent).
3. Add `front-page.php` + template parts rendering from the new data.
4. Verify section-by-section HTML/visual parity with the pre-change render (and with `docs/html/index.html` / `docs/homepage-content.html`).
5. Empty the static `wp:html` block markup from Home page post 5 once parity is confirmed.
6. Rollback: the old markup snapshot stays at `docs/homepage-content.html`; reverting the theme files + restoring post 5 content restores the static version. CPT data is additive and can remain.

## Content Model Mapping

Final mapping of homepage sections to the content model, as implemented (per-section markup lives in `template-parts/home/`):

| Homepage section | Content type (admin menu) | Fields used |
| --- | --- | --- |
| Hero | Options — Settings → Homepage Settings (`mp_homepage_settings`) | Hero headline, subline, primary CTA label+link, secondary CTA label+link, hero image + image alt |
| Trust strip | `trust_stat` CPT ("Trust Facts") | Title (value), Excerpt (caption), Order |
| Sectors ("Furniture Made for Commercial Interiors") | `sector` taxonomy ("Sectors", on Projects) | Name, description (card body), `sector_image`, `sector_order` (sector number "• 01" derived from order; card headings/links fixed in template) |
| Product ranges ("The Full Commercial Range") | `range` CPT ("Product Ranges") | Title, Excerpt, Featured Image, Order (first 6 = hospitality column, last 6 = retail column) |
| Ethos band | Options — Homepage Settings | Ethos eyebrow, quote, body |
| Projects ("Recent Venue & Retail Projects") | `project` CPT ("Projects") | Title, Excerpt ("Short Description"), Featured Image ("Card Image"), "Location • Type" meta, `project_tag` terms, Order (Order 0 = lead card) |
| Capabilities ("One Workshop…") | `capability` CPT ("Capabilities") | Title, Excerpt, Order |
| Process ("From Brief to Opening Day") | `step` CPT ("Process Steps") | Title, Excerpt, "Week Label" meta, Order (step number 01–04 derived from position) |
| Client stories ("What Our Clients Say") | `testimonial` CPT ("Client Stories") | Content (the quote), Title (Name / Company), "Author Role", "City", "Star Rating" (1–5), Order |
| Consultation intro | Options — Homepage Settings | Consultation eyebrow, headline, intro (enquiry form shortcode + contact rows stay fixed in the template) |
| Project card tags | `project_tag` taxonomy ("Materials & Features", on Projects) | Term name |

All repeatable entries sort by `menu_order` ASC (date breaks ties), matching the "Order" field shown to editors. Editor-facing labels and field help text are defined in `inc/content-model.php`.

## Open Questions

- Use ACF for the extra meta fields (friendlier labels) vs. native meta boxes? (Design assumes native; ACF is a drop-in preference if the user wants it.)
- Should `range` and `capability` be one CPT with a `type` taxonomy, or two CPTs? (Design assumes two CPTs for simplicity.)
- Do the trust stats need more than 4 items or images? (Design assumes 4 text-only stats; easy to extend.)