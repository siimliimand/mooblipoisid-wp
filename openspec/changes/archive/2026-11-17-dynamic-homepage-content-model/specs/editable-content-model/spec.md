# editable-content-model Specification

## Purpose

Defines the structured, admin-editable content model behind the homepage: the custom post types, taxonomies and options screen that let a non-technical editor maintain the homepage's projects, testimonials, product ranges, capabilities, process steps, trust stats, sectors and static text without editing code or HTML.

## Requirements

### Requirement: Project Entries

The site SHALL provide a `project` custom post type for featured project cards. Each project SHALL have a title, a featured image, a short description, a "Location • Type" text line and one or more material/feature tags. Projects SHALL be creatable and editable from the WordPress admin using plain, non-technical labels, and SHALL be orderable.

#### Scenario: Editor adds a project

- **WHEN** an editor creates a new Project with a title, featured image, short description, location/type line and tags
- **THEN** the project appears as a card on the homepage without any code change

#### Scenario: Featured image editing

- **WHEN** an editor sets a project's featured image
- **THEN** that image is used for the project card via the standard "Set featured image" control

### Requirement: Testimonial Entries

The site SHALL provide a `testimonial` custom post type for client quotes. Each testimonial SHALL have the quote text, an author role, a city and a numeric star rating, editable from the admin.

#### Scenario: Editor adds a testimonial

- **WHEN** an editor creates a Testimonial with quote text, role, city and rating
- **THEN** the testimonial appears as a quote card with the correct number of stars on the homepage

### Requirement: Ordered List Entries (Ranges, Capabilities, Process Steps)

The site SHALL provide `range`, `capability` and `step` custom post types for the product-range, capability and process-step lists. Each entry SHALL have a title and a description; `step` entries SHALL additionally have a "Week xx – xx" label. Each list SHALL be orderable via a numeric order field.

#### Scenario: Editor reorders process steps

- **WHEN** an editor changes the order values of the process steps
- **THEN** the homepage displays the steps in the new order

#### Scenario: Editor adds a capability

- **WHEN** an editor adds a new Capability with a title and description
- **THEN** a new capability card appears in the capabilities section

### Requirement: Trust Stat Entries

The site SHALL provide a `trust_stat` custom post type (or equivalent option group) for the trust-strip facts. Each entry SHALL have a value (e.g. "18 Years") and a caption, and SHALL be orderable.

#### Scenario: Editor updates a trust fact

- **WHEN** an editor updates a trust stat's value and caption
- **THEN** the trust strip reflects the new text

### Requirement: Sector Taxonomy

The site SHALL provide a `sector` taxonomy (Hospitality, Retail, Private) with a term description, an image and a sort order. The sectors section SHALL render one card per sector term, and projects SHALL be taggable by sector.

#### Scenario: Sector cards render from terms

- **WHEN** the homepage is viewed
- **THEN** the sectors section shows one card per sector term using the term's description and image

### Requirement: Static Text Options

The site SHALL provide a Homepage Settings options screen holding the one-off static text: hero headline, hero subline, hero call-to-action labels and links, ethos quote and body, and the consultation intro. These SHALL be editable from the admin and rendered on the homepage.

#### Scenario: Editor updates the hero headline

- **WHEN** an editor changes the hero headline text in Homepage Settings
- **THEN** the homepage hero displays the new headline

### Requirement: Non-Technical Editing Conventions

All content entries SHALL use clear, plain-language labels and standard WordPress editing controls (title, short description/excerpt, featured image, order) so that a non-technical editor can maintain the homepage without editing HTML, shortcodes or code.

#### Scenario: Non-technical editor updates content

- **WHEN** a non-technical editor edits any homepage content entry
- **THEN** they interact only with labelled fields and standard media/order controls

### Requirement: Translatable Content Registrations

All custom post types and the sector taxonomy SHALL be registered as public and shown in the admin UI, so that a later multilingual plugin can recognise and translate them.

#### Scenario: Multilingual plugin recognises the content types

- **WHEN** a multilingual plugin is later installed and configured
- **THEN** the `project`, `testimonial`, `range`, `capability`, `step`, `trust_stat` types and the `sector` taxonomy appear as translatable