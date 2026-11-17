# front-end-design Specification

## Purpose
TBD - created by archiving change setup-wordpress-kadence. Update Purpose after archive.
## Requirements
### Requirement: Design Tokens Match Prototype

The Kadence theme globals SHALL be configured so the site's colors and typography match `docs/html/index.html`: warm brown/sand palette (primary `#714a23`, primary container `#8c6239`, surface tones from white `#ffffff` through `#fbf9f6` to dark `#30312f`, inverse-on-surface `#f2f0ed`), Playfair Display for display/heading type, Plus Jakarta Sans for body/label type, and small-radius (0.25rem) corners.

#### Scenario: Visual comparison with prototype

- **WHEN** the built homepage is compared side-by-side with `docs/html/index.html`
- **THEN** background, text, accent and button colors, and the heading/body font pairing are visually consistent with the prototype

### Requirement: Site Header

The site header SHALL show the Mööblipoisid logo/brand name, a main navigation with anchors to the main sections (Hospitality, Retail, Projects, Capabilities, Process, About), and a prominent "Request a Quote" button linking to the enquiry section. The header SHALL be sticky and collapse the navigation into a mobile menu on small screens.

#### Scenario: Desktop visitor opens the homepage

- **WHEN** the homepage loads on a desktop viewport
- **THEN** the header displays the Mööblipoisid brand, all navigation anchors and the "Request a Quote" button

#### Scenario: Mobile visitor opens the homepage

- **WHEN** the homepage loads on a mobile viewport
- **THEN** navigation items are reachable through a mobile menu control instead of an inline row

### Requirement: Homepage Sections

The homepage SHALL present, in order, the same sections as the prototype. Each section SHALL be rendered dynamically from the editable content model (custom post types, taxonomies and the Homepage Settings options screen) rather than hardcoded markup, while producing the same visual output:

1. Hero — full-width background image, headline about custom furniture for venues, subline naming cafés, restaurants and retail, plus "Request a Quote" and "See Commercial Projects" calls to action (text from Homepage Settings)
2. Trust strip — quick facts (experience, in-house workshops, built-for-service durability, out-of-hours install) from `trust_stat` entries
3. Sectors — cards per `sector` term: Hospitality (large), Retail & Shopfitting, Private Kitchens
4. Product ranges — two-column lists of hospitality and retail products from `range` entries
5. Featured projects — project cards with images and short descriptions from `project` entries
6. Capabilities — cards from `capability` entries: CNC woodworking, metalworking, stone fabrication, commercial-grade surfaces
7. Process — numbered steps from `step` entries, from brief/site survey to installation & handover
8. Ethos band — quality statement with in-house production badge (text from Homepage Settings)
9. Testimonials — client quotes from `testimonial` entries
10. Consultation section — enquiry invitation with manager contacts (intro from Homepage Settings) and the enquiry form
11. Footer — brand block, legal data, explore links, request-a-quote CTA

#### Scenario: Section order check

- **WHEN** the homepage is scrolled from top to bottom
- **THEN** all eleven sections appear in the order listed above

#### Scenario: Content drives rendering

- **WHEN** an editor adds, edits or removes a project, testimonial, range, capability, process step, trust stat or sector
- **THEN** the corresponding homepage section updates to reflect the change without editing markup

#### Scenario: Visual parity with prototype

- **WHEN** the dynamically rendered homepage is compared with the previous static version
- **THEN** the layout, typography, colors and spacing are unchanged and `style.css` requires no modification

### Requirement: Real Company Content Only

All homepage copy SHALL use real Mööblipoisid content: the Mööblipoisid brand, Mööblipoisid OÜ legal name, real manager contacts (Andre Merila, Marilyn Salu), and the real legal data (Reg. Nr 12340882, Võidu 75, LHV account). Fictional prototype elements — invented persons, foreign addresses, guild certifications, foreign phone numbers — SHALL NOT appear in the delivered site.

#### Scenario: Placeholder audit

- **WHEN** the delivered homepage is reviewed against the brand-identity spec
- **THEN** no fictional persons, foreign studios or invented credentials are present

### Requirement: Placeholder Imagery Policy

Section imagery from the prototype MAY be used temporarily, but every image SHALL carry a meaningful alt text describing the intended real photograph, and real project photography SHALL replace prototype images before launch.

#### Scenario: Image inventory

- **WHEN** the homepage is audited for images
- **THEN** each image has descriptive alt text and is tracked as either final or placeholder-for-replacement

