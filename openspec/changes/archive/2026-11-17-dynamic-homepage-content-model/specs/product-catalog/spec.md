# product-catalog — Delta

## MODIFIED Requirements

### Requirement: Hospitality Product Range

The site SHALL present the hospitality range (cafés, bars, restaurants), covering at minimum:

- Custom bar and service counters, often integrated with refrigeration, sinks and espresso stations
- Built-in banquette and booth seating
- Commercial-grade dining tables in scratch- and heat-resistant materials (compact laminate, sintered stone, treated hardwood)
- Barstools and chairs
- Waiter stations
- Acoustic wall paneling

The hospitality product categories SHALL be maintained as editable `range` entries (or hospitality `sector` content) so that an editor can add, edit or reorder them without editing code.

#### Scenario: Café owner reviews the hospitality range

- **WHEN** a visitor opens the hospitality section
- **THEN** all listed hospitality product categories are visible in Estonian, Russian and English

#### Scenario: Editor maintains the hospitality range

- **WHEN** an editor adds or reorders a hospitality product category
- **THEN** the hospitality section reflects the change without editing markup

### Requirement: Retail Product Range

The site SHALL present the retail range (shopfitting), covering at minimum:

- Point-of-sale (POS) checkout counters
- Modular display shelving
- Clothing racks
- Illuminated glass display cases
- Fitting room joinery
- Branded feature walls

The retail product categories SHALL be maintained as editable `range` entries (or retail `sector` content) so that an editor can add, edit or reorder them without editing code.

#### Scenario: Retail brand reviews the shopfitting range

- **WHEN** a visitor opens the retail section
- **THEN** all listed retail product categories are visible in Estonian, Russian and English

### Requirement: In-House Capabilities

The site SHALL communicate that wood (CNC woodworking), metal and stone fabrication are all produced in-house, as this is a core differentiator: customers receive a complete matching package from one team. The capability cards SHALL be maintained as editable `capability` entries so that an editor can add, edit or reorder them without editing code.

#### Scenario: One-team message

- **WHEN** a visitor reads the capabilities section
- **THEN** the in-house wood, metal and stone production is described and tied to the product ranges

#### Scenario: Editor maintains capabilities

- **WHEN** an editor adds or reorders a capability entry
- **THEN** the capabilities section reflects the change without editing markup