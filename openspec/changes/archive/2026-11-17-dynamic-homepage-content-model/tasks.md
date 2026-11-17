# Tasks — Dynamic Homepage Content Model

## 1. Register content model (theme code)

- [x] 1.1 Register `project`, `testimonial`, `range`, `capability`, `step`, `trust_stat` custom post types in `functions.php` (public + show_ui, supports title/excerpt/thumbnail/page-attributes, `has_archive => false`, plain non-technical labels)
- [x] 1.2 Register `sector` taxonomy (on `project`) with description/image/order support and `project_tag` taxonomy for material/feature tags
- [x] 1.3 Add the extra meta fields with clear labels: project "Location • Type", testimonial role/city/rating, step "Week xx – xx", trust_stat value/caption (native meta boxes or ACF)
- [x] 1.4 Add the Homepage Settings options screen (hero headline/sub/CTA labels + hrefs, ethos quote/body, consultation intro + manager lines) with a plain-language admin page

## 2. One-time content migration

- [x] 2.1 Write an idempotent import script (`wp eval`/CLI) that parses `docs/homepage-content.html` / the current `wp:html` blocks and creates the project/testimonial/range/capability/step/trust_stat posts and sector terms
- [x] 2.2 Populate Homepage Settings options from the hero/ethos/consultation text in the current blocks; attach existing uploaded images to the matching entries
- [x] 2.3 Run the import and confirm all expected posts/terms/options exist with correct content

## 3. Dynamic rendering (templates)

- [x] 3.1 Add `front-page.php` in the child theme that renders the 11 sections in the current order
- [x] 3.2 Build template parts for the data-driven sections (trust, sectors, ranges, projects, capabilities, process, stories) that query the CPTs/terms and emit the exact current markup + `mp-*` classes
- [x] 3.3 Build template parts for the static sections (hero, ethos/maker, consultation intro) that read Homepage Settings and keep the `[mooblipoisid_enquiry_form]` shortcode inside `#consultation`
- [x] 3.4 Ensure ordering is stable (menu_order ASC, then date) and the projects lead card renders first

## 4. Verification & cutover

- [x] 4.1 Compare rendered HTML/visuals section-by-section against the pre-change render and `docs/html/index.html`; confirm `style.css` is unchanged and the page is pixel-identical
- [x] 4.2 Test non-technical editing: add/edit/reorder a project, testimonial, range, capability, step, trust stat and sector, and confirm each updates the homepage
- [x] 4.3 Empty the static `wp:html` block markup from Home page (post 5) once parity is confirmed; verify the enquiry form still submits (incl. attachment) and no PHP errors appear
- [x] 4.4 Confirm the new CPTs/taxonomy are registered `public` and would be visible to a future multilingual plugin (spot-check registration flags)

## 5. Documentation

- [x] 5.1 Add a short editor README describing how to maintain each homepage section in admin
- [x] 5.2 Update `docs/homepage-content.html` note (now a reference snapshot) and record the content model mapping in the change docs