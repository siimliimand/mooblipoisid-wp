# Multilingual Content Specification

## Purpose

Defines how the site serves content in three languages: Estonian (default), Russian and English. Every visitor-facing page, menu and form must exist in all three languages.

## Requirements

### Requirement: Default Language Estonian

The site's default language SHALL be Estonian (`et`). Requests without a language preference SHALL be served in Estonian.

#### Scenario: First visit without language preference

- **WHEN** a visitor opens the site for the first time
- **THEN** the content is displayed in Estonian

### Requirement: Supported Languages

The site SHALL additionally support Russian (`ru`) and English (`en`). Each language SHALL be reachable via a distinct URL path or subdirectory (e.g. `/ru/`, `/en/`).

#### Scenario: Switching to Russian

- **WHEN** a visitor selects Russian in the language switcher
- **THEN** the same page is served in Russian under the Russian URL path

### Requirement: Full Translation Coverage

All visitor-facing content SHALL exist in all three languages: pages, product/service descriptions, menus, forms (labels, placeholders, validation messages), the enquiry feedback message and the footer legal information.

#### Scenario: Untranslated content check

- **WHEN** any page is opened in Russian or English
- **THEN** no Estonian-only strings appear in that page's visitor-facing content

### Requirement: Localised Navigation

The main navigation, footer menus and internal links SHALL be localised per language, so that navigating within one language never lands the visitor on a page in another language.

#### Scenario: Navigating inside the English version

- **WHEN** a visitor follows any navigation or internal link while browsing the English version
- **THEN** the destination page is also in English

### Requirement: Per-Language SEO

Each language version SHALL have its own crawlable URLs, page titles and meta descriptions, and SHALL declare the available language alternatives (e.g. `hreflang` annotations) to search engines.

#### Scenario: Search engine indexes language versions

- **WHEN** a search engine crawls the Estonian version of a page
- **THEN** it can discover the corresponding `ru` and `en` versions via language-alternate annotations
