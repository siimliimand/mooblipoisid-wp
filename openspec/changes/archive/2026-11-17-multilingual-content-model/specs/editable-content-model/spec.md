# editable-content-model — Delta Specification

## MODIFIED Requirements

### Requirement: Static Text Options

The site SHALL provide a Homepage Settings options screen holding the one-off static text: hero headline, hero subline, hero call-to-action labels and links, ethos quote and body, and the consultation intro. These SHALL be editable from the admin and rendered on the homepage. Each static text field SHALL exist per language, and the homepage SHALL render the value for the active language (falling back to the default language when a translation is missing).

#### Scenario: Editor updates the hero headline

- **WHEN** an editor changes the hero headline text in Homepage Settings
- **THEN** the homepage hero displays the new headline

#### Scenario: Editor sets a per-language hero headline

- **WHEN** an editor sets a distinct hero headline for a specific language
- **THEN** that language's homepage hero displays the language-specific headline

### Requirement: Translatable Content Registrations

All custom post types and the `sector` and `project_tag` taxonomies SHALL be registered as public and shown in the admin UI, and SHALL be marked translatable so the multilingual engine recognises and translates them.

#### Scenario: Multilingual plugin recognises the content types

- **WHEN** a multilingual plugin is installed and configured
- **THEN** the `project`, `testimonial`, `range`, `capability`, `step`, `trust_stat` types and the `sector` and `project_tag` taxonomies appear as translatable

#### Scenario: Editor creates a translation

- **WHEN** an editor opens a content entry and creates a translation for another language
- **THEN** the entry is linked to its per-language translation and rendered on that language's homepage