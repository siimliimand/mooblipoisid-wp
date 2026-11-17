# Dynamic Homepage Content Model

## Why

The homepage currently renders its 10 sections from raw HTML hardcoded inside `core/html` blocks in a single page's post content. Nothing is editable by a non-technical person — adding a project, changing a testimonial or reordering process steps means hand-editing markup. This blocks the plan to make most sections dynamic (custom post types / taxonomies) and to later add Estonian/Russian/English translation, since translating opaque HTML is error-prone and keeps layout and copy tangled. Restructuring the content into structured, admin-editable entries *before* adding Polylang means the translation plugin will operate on real posts and terms from day one instead of throwaway HTML.

## What Changes

- Register custom post types for the repeatable homepage content: `project`, `testimonial`, `range`, `capability`, `step`, `trust_stat`. Each is `public`, supports featured image and ordering, and uses plain, non-technical labels and fields.
- Register a `sector` taxonomy (Hospitality / Retail / Private) used by the sectors section and to tag projects, with term description + image + sort order.
- Register a `project_tag` taxonomy for the material/feature tags shown on project cards (e.g. "Smoked Oak", "Sintered Stone Top").
- Add a **Homepage Settings** options screen for the static one-off text (hero headline/sub/CTAs, ethos quote/body, consultation intro) so it is edited in admin, not code.
- Build `front-page.php` + per-section template parts in the child theme that query the CPTs/terms and reproduce the **exact current markup and CSS classes** (`mp-project`, `mp-quote`, `mp-sector`, etc.) so the rendered homepage is pixel-identical to today.
- Migrate the existing content currently embedded in `wp:html` blocks into the new posts/terms via a one-time import, then empty the static block markup from the Home page.
- Keep the enquiry form shortcode and footer widgets working unchanged; only their surrounding section wrappers move into templates.

No behaviour is lost: the same 11 sections render in the same order with the same design. What changes is *where the content lives* (admin-editable posts/terms/options instead of hardcoded HTML).

## Capabilities

### New Capabilities
- `editable-content-model`: The structured, admin-editable content model for the homepage — custom post types, taxonomies and the Homepage Settings options screen, including field definitions, ordering and non-technical editing conventions.

### Modified Capabilities
- `front-end-design`: The homepage sections requirement changes from "static markup matching the prototype" to "the same sections rendered dynamically from the editable content model". Section order, design and visual output remain identical; the source of the content changes.
- `product-catalog`: The product ranges, capabilities and sector offerings become managed entries (CPT `range`, CPT `capability`, taxonomy `sector`) rather than fixed list text, so editors can add/remove/reorder range and capability items.

## Impact

- **Code**: child theme `functions.php` (CPT/taxonomy/options registration, one-time import), new `front-page.php` + `template-parts/` section templates, existing `style.css` reused unchanged.
- **Content**: Home page (post 5) `wp:html` block markup is migrated into posts/terms and then cleared; `docs/homepage-content.html` remains the reference snapshot.
- **Data**: new DB content (CPT + taxonomy + options). Backwards-compatible — no public URL or layout change.
- **Dependencies**: none required now. Positions for later `multilingual-content` work (Polylang free + Loco Translate), which is explicitly out of scope for this change.
- **Systems**: local DDEV + production WP; content editing moves into WP admin screens.