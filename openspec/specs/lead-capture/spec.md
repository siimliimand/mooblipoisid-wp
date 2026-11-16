# Lead Capture Specification

## Purpose

Defines how the site converts visitors into project enquiries: contact details for the two managers, a project enquiry form, and clear response expectations. This is the primary conversion goal of the site.

## Requirements

### Requirement: Contact Details Display

The site SHALL display Mööblipoisid's contact details on the contact/consultation section and in the footer:

- Production Manager: Andre Merila — andre@mooblipoisid.ee — +372 515 3229
- Project Manager: Marilyn Salu — marilyn@mooblipoisid.ee — +372 55 910 872
- Address: Võidu 75, 11212 Tallinn, Estonia

#### Scenario: Visitor looks up a phone number

- **WHEN** a visitor opens the contact section or footer on any page
- **THEN** both managers' phone numbers and email addresses are visible

### Requirement: Project Enquiry Form

The site SHALL provide a project enquiry form collecting at minimum: name, company/venue, email, phone, project type (hospitality / retail / counters-seating only / multi-site / private kitchen), target opening or install window, venue location and a brief with dimensions.

#### Scenario: Visitor submits an enquiry

- **WHEN** a visitor fills in the required fields and submits the form
- **THEN** the enquiry is accepted and the visitor sees a confirmation message

#### Scenario: Invalid submission

- **WHEN** a visitor submits the form with required fields missing or an invalid email
- **THEN** the form is not sent and the visitor is shown field-level validation messages

### Requirement: Enquiry Delivery

Every submitted enquiry SHALL be delivered to the project managers' email addresses (andre@mooblipoisid.ee, marilyn@mooblipoisid.ee) so that no enquiry is lost if the CMS is unavailable.

#### Scenario: Enquiry reaches the team

- **WHEN** an enquiry is submitted successfully
- **THEN** an email containing all submitted field values arrives at the project managers' mailbox

### Requirement: Attachment Uploads

The enquiry form SHALL allow attaching brief materials — floor plans, drawings or concept boards (PDF, DWG, PNG, JPEG, up to ~25 MB) — and SHALL deliver them with the enquiry.

#### Scenario: Architect attaches drawings

- **WHEN** a visitor attaches a PDF floor plan under the size limit and submits the form
- **THEN** the attachment is delivered together with the enquiry

### Requirement: Response Expectation

The site SHALL set the expectation that enquiries receive initial feedback and a budget direction within 48 hours.

#### Scenario: Confirmation messaging

- **WHEN** an enquiry is submitted
- **THEN** the confirmation message states that the brief will be reviewed within 48 hours
