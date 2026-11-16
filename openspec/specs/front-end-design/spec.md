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

The homepage SHALL present, in order, the same sections as the prototype:

1. Hero — full-width background image, headline about custom furniture for venues, subline naming cafés, restaurants and retail, plus "Request a Quote" and "See Commercial Projects" calls to action
2. Trust strip — four quick facts (experience, in-house workshops, built-for-service durability, out-of-hours install)
3. Sectors — three cards: Hospitality (large), Retail & Shopfitting, Private Kitchens
4. Product ranges — two-column lists of hospitality and retail products
5. Featured projects — project cards with images and short descriptions
6. Capabilities — four cards: CNC woodworking, metalworking, stone fabrication, commercial-grade surfaces
7. Process — four numbered steps from brief/site survey to installation & handover
8. Ethos band — quality statement with in-house production badge
9. Testimonials — client quotes
10. Consultation section — enquiry invitation with manager contacts and the enquiry form
11. Footer — brand block, legal data, explore links, request-a-quote CTA

#### Scenario: Section order check

- **WHEN** the homepage is scrolled from top to bottom
- **THEN** all eleven sections appear in the order listed above

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

