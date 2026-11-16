# Brand Identity Specification

## Purpose

Defines the company identity facts that must be used consistently across the site: legal and brand names, registry data, addresses and credentials. Placeholder content from the design prototype (fictional persons, foreign addresses, invented certifications) must not remain anywhere.

## Requirements

### Requirement: Company and Brand Names

The site SHALL use **Mööblipoisid** as the brand name in the logo, header and hero areas, and **Mööblipoisid OÜ** as the legal company name in legal/footer contexts (copyright line, terms, invoices-relevant pages).

#### Scenario: Footer copyright

- **WHEN** a visitor views the footer
- **THEN** the copyright line names Mööblipoisid OÜ

### Requirement: Legal Data Block

The footer SHALL display the company's legal data: registry number 12340882, legal address Võidu 75, 11212 Tallinn, Estonia, and bank account EE437700771003181572 (LHV).

#### Scenario: Client verifies the company

- **WHEN** a business partner opens the footer to verify the company
- **THEN** the registry number and legal address are visible and correct

### Requirement: Team Attribution

The site SHALL attribute production matters to Andre Merila (Production Manager) and project matters to Marilyn Salu (Project Manager). No fictional persons SHALL be presented as staff anywhere on the site.

#### Scenario: Enquiry routing

- **WHEN** a visitor reads the contact section or submits an enquiry
- **THEN** the contacts shown are Andre Merila and Marilyn Salu with their real phone numbers and mooblipoisid.ee email addresses

### Requirement: Authentic Credentials

Any certifications, credentials or founding-year claims shown on the site SHALL be real and verifiable. If none exist yet, such claims SHALL be omitted rather than replaced with invented ones.

#### Scenario: Placeholder credentials removed

- **WHEN** the site content is reviewed
- **THEN** no invented certifications, guilds, foreign addresses or founding dates appear
