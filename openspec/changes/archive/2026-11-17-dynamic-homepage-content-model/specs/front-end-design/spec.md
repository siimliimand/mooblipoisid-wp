# front-end-design — Delta

## MODIFIED Requirements

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