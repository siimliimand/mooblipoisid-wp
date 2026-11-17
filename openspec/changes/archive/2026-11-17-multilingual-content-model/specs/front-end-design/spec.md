# front-end-design — Delta Specification

## ADDED Requirements

### Requirement: Language Switcher Design

The language switcher SHALL be rendered in the site header and footer and SHALL be styled consistently with the design tokens (typography, colours, spacing) defined for the site. It SHALL present the available languages (`et`, `ru`, `en`) and indicate the active language.

#### Scenario: Switcher visible on the homepage

- **WHEN** the homepage is viewed
- **THEN** the language switcher appears in the header and footer, matching the design system

#### Scenario: Active language indicated

- **WHEN** a visitor is browsing the English version
- **THEN** the switcher shows English as the active language

### Requirement: Per-Language Homepage Rendering

The homepage sections SHALL render their content resolved by the active language, while preserving the design markup and layout for every language. Switching languages SHALL change the content but not the structure or styling of the page.

#### Scenario: Consistent layout across languages

- **WHEN** the homepage is viewed in Estonian, Russian and English
- **THEN** the section structure, layout and styling are identical across the three, differing only in the rendered text