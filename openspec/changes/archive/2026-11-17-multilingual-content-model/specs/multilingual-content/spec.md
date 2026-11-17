# multilingual-content — Delta Specification

## ADDED Requirements

### Requirement: Language Engine and Configuration

The site SHALL use a multilingual engine (Polylang free) configured with three languages: Estonian (`et`, default), Russian (`ru`) and English (`en`). Estonian SHALL be the default language, and requests without a language preference SHALL be served in Estonian.

#### Scenario: First visit without language preference

- **WHEN** a visitor opens the site for the first time
- **THEN** the content is displayed in Estonian

#### Scenario: Languages available

- **WHEN** the multilingual engine is configured
- **THEN** `et`, `ru` and `en` are registered, with `et` marked as the default

### Requirement: Subdirectory Language URLs

Each language SHALL be reachable via a distinct subdirectory URL path: Estonian at the root (`/`), Russian at `/ru/`, and English at `/en/`.

#### Scenario: Switching to Russian

- **WHEN** a visitor selects Russian in the language switcher
- **THEN** the same page is served in Russian under the `/ru/` URL path

#### Scenario: English URL path

- **WHEN** a visitor opens the English version of a page
- **THEN** it is served under the `/en/` URL path

### Requirement: Language Switcher

The site SHALL provide a language switcher, available in the header and footer, that lets a visitor move between the `et`, `ru` and `en` versions of the current page. The switcher SHALL be styled consistently with the design system.

#### Scenario: Switcher preserves the current page

- **WHEN** a visitor uses the language switcher on a given page
- **THEN** the corresponding translation of that same page is shown in the selected language

### Requirement: Translatable Content Model

All homepage content model entities SHALL be language-aware: the `project`, `testimonial`, `range`, `capability`, `step` and `trust_stat` custom post types and the `sector` and `project_tag` taxonomies SHALL each have per-language translations, and the homepage SHALL render content in the active language.

#### Scenario: Per-language project content

- **WHEN** the homepage is viewed in English
- **THEN** the project cards show the English project titles, descriptions and tags

#### Scenario: Per-language sector cards

- **WHEN** the homepage is viewed in Russian
- **THEN** the sectors section shows the Russian sector names and descriptions

### Requirement: Per-Language Static Text

The Homepage Settings static text (hero headline, hero subline, hero CTA labels and links, ethos quote and body, consultation intro) SHALL exist per language. The homepage SHALL render these strings in the active language, falling back to Estonian for any missing translation.

#### Scenario: Per-language hero headline

- **WHEN** an editor sets a distinct hero headline for English
- **THEN** the English homepage hero displays the English headline

#### Scenario: Fallback for missing translation

- **WHEN** a Homepage Settings field has no Russian translation
- **THEN** the Russian homepage shows the Estonian value for that field

### Requirement: Full Translation Coverage

All visitor-facing content SHALL exist in all three languages: pages, product/service descriptions, menus, forms (labels, placeholders, validation messages), the enquiry feedback message and the footer legal information. Theme gettext strings SHALL be translated via a translation tool (Loco Translate) into `ru` and `en`.

#### Scenario: Untranslated content check

- **WHEN** any page is opened in Russian or English
- **THEN** no Estonian-only strings appear in that page's visitor-facing content

#### Scenario: Form strings translated

- **WHEN** the enquiry form is displayed in English
- **THEN** its labels, placeholders and validation/success messages are in English

### Requirement: Localised Navigation

The main navigation, footer menus and internal links SHALL be localised per language, so that navigating within one language never lands the visitor on a page in another language. Each menu location SHALL have a per-language menu.

#### Scenario: Navigating inside the English version

- **WHEN** a visitor follows any navigation or internal link while browsing the English version
- **THEN** the destination page is also in English

### Requirement: Per-Language SEO

Each language version SHALL have its own crawlable URLs, page titles and meta descriptions, and SHALL declare the available language alternatives (e.g. `hreflang` annotations) to search engines.

#### Scenario: Search engine indexes language versions

- **WHEN** a search engine crawls the Estonian version of a page
- **THEN** it can discover the corresponding `ru` and `en` versions via language-alternate annotations

#### Scenario: Per-language titles

- **WHEN** a page is viewed in Russian
- **THEN** its document title and meta description are in Russian