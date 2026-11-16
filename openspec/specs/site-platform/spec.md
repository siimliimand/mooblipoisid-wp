# Site Platform Specification

## Purpose

Defines the technical foundation of the Mööblipoisid website: the CMS, the active theme, the local development environment and the production hosting target. All other capabilities (content, languages, lead capture) depend on this foundation.

## Requirements

### Requirement: WordPress CMS

The site SHALL run on self-hosted WordPress as its content management system, so that non-developers can edit content without touching code.

#### Scenario: Content editor publishes a page

- **WHEN** an editor creates or edits a page in the WordPress admin
- **THEN** the published content is served on the live site without a code deployment

### Requirement: Kadence Theme

The site SHALL use the [Kadence](https://wordpress.org/themes/kadence/) theme as the active theme. Custom code that cannot live in Kadence settings SHALL go into a Kadence child theme, so theme updates do not overwrite project customisations.

#### Scenario: Theme update

- **WHEN** the Kadence theme is updated to a new version
- **THEN** project-specific customisations survive the update because they live in the child theme and theme settings, not in modified theme files

### Requirement: Local Development Environment

Local development SHALL run in DDEV (Docker-based), providing a PHP + MySQL stack equivalent to production. A plain Docker Compose setup MAY be used as an alternative.

#### Scenario: New developer starts the site locally

- **WHEN** a developer runs `ddev start` in a fresh clone of the repository
- **THEN** a working WordPress instance is reachable at the project's `.ddev.site` URL without manual PHP or database installation

### Requirement: Production Hosting

The production site SHALL be hosted on zone.ee shared PHP hosting and served over HTTPS with a valid certificate.

#### Scenario: Visitor opens the production site

- **WHEN** a visitor opens the production domain in a browser
- **THEN** the site is served over HTTPS without certificate warnings
