## 1. Local Environment

- [x] 1.1 Initialize DDEV project (`ddev config --project-name=mooblipoisid --project-type=wordpress --docroot=public --php-version=8.3`) and commit `.ddev/config.yaml`
- [x] 1.2 Create plain Docker fallback `docker-compose.yml` (wordpress:php8.3-apache + mariadb) matching DDEV behavior
- [x] 1.3 Add `.gitignore` entries: `public/wp-config.php`, `public/wp-content/uploads/`, DDEV `.dbimage`/transient files
- [x] 1.4 `ddev start` and verify the environment boots

## 2. WordPress Install

- [x] 2.1 Download WordPress core into `public/` (`ddev wp core download --locale=et_ET`) and commit it
- [x] 2.2 Create `public/wp-config-sample.php` with sane defaults and commit; generate real `wp-config.php` (gitignored) with DDEV DB credentials and et_ET locale
- [x] 2.3 Run `ddev wp core install --url=https://mooblipoisid.ddev.site --title="Mööblipoisid" --skip-email` with admin account
- [x] 2.4 Verify wp-admin and front-end load at `https://mooblipoisid.ddev.site`

## 3. Theme Installation

- [x] 3.1 Install and activate Kadence (`ddev wp theme install kadence --activate`)
- [x] 3.2 Create `mooblipoisid-child` child theme in `public/wp-content/themes/` (style.css header, functions.php enqueueing parent + child styles, committed)
- [x] 3.3 Activate child theme (`ddev wp theme activate mooblipoisid-child`) and verify parent styles still load
- [x] 3.4 Install Kadence Blocks plugin (`ddev wp plugin install kadence-blocks --activate`)

## 4. Design Tokens (match prototype)

- [x] 4.1 Set Kadence global colors to the prototype palette (primary `#714a23`, primary container `#8c6239`, surface `#fbf9f6`, surface-container tones, inverse-on-surface `#f2f0ed`, dark `#30312f`)
- [x] 4.2 Set Kadence typography: Playfair Display for headings/display, Plus Jakarta Sans for body/labels, load via Kadence font settings
- [x] 4.3 Mirror the palette as CSS custom properties in `mooblipoisid-child/style.css`
- [x] 4.4 Configure base button styles (small radius, primary bg, uppercase label) to match prototype CTAs
- [x] 4.5 Export customizer backup (`.dat`) to `docs/` and commit

## 5. Site Structure

- [x] 5.1 Set site identity: title Mööblipoisid, tagline, brand logo placeholder; language et_ET
- [x] 5.2 Create menus: main nav (Hospitality, Retail, Projects, Capabilities, Process, About) with section anchors; footer "Explore" menu
- [x] 5.3 Create empty `Home` page, set it as static front page (`ddev wp option update show_on_front page` + `page_on_front`)

## 6. Homepage Sections (match `docs/html/index.html`)

- [x] 6.1 Hero: full-width background image, headline, subline, "Request a Quote" + "See Commercial Projects" CTAs (anchor to `#consultation` / `#projects`)
- [x] 6.2 Trust strip: four facts row (experience, in-house workshops, durability, out-of-hours install)
- [x] 6.3 Sectors: three cards — Hospitality (large), Retail & Shopfitting, Private Kitchens — each linking to its section or `#consultation`
- [x] 6.4 Product ranges: two-column hospitality and retail product lists (counters, banquettes, tables, barstools, waiter stations, acoustic panels / POS, shelving, racks, glass cases, fitting rooms, feature walls)
- [x] 6.5 Featured projects: cards grid with images and short descriptions (anchors `#projects`)
- [x] 6.6 Capabilities: four cards — CNC woodworking, metalworking, stone fabrication, commercial-grade surfaces
- [x] 6.7 Process: four numbered steps (brief & site survey → drawings → production → install & handover)
- [x] 6.8 Ethos band: quality statement + "Wood, metal & stone under one roof" badge
- [x] 6.9 Testimonials: two client quote cards
- [x] 6.10 Consultation section: manager contact blocks (Andre Merila +372 515 3229 andre@mooblipoisid.ee; Marilyn Salu +372 55 910 872 marilyn@mooblipoisid.ee; Võidu 75 workshop note) and Kadence form block (name, company, email, phone, project type, timeline, location, brief, attachments notice) with 48-hour response message
- [x] 6.11 Footer: brand block with Mööblipoisid OÜ, legal data (Reg. Nr 12340882, Võidu 75 11212 Tallinn, LHV EE437700771003181572), explore menu, Request a Quote CTA, copyright "© Mööblipoisid OÜ"

## 7. Verification

- [x] 7.1 Side-by-side compare homepage with `docs/html/index.html` — colors, fonts, section order per front-end-design spec
- [x] 7.2 Mobile check: header collapses to mobile menu, sections stack cleanly
- [x] 7.3 Placeholder audit: no fictional persons, foreign addresses, invented credentials, or old prototype brand anywhere
- [x] 7.4 Submit a test enquiry through the form and confirm it reaches both manager mailboxes (note deliverability follow-up if it fails)
- [x] 7.5 Update README.md quick-start if the actual commands differ from documented ones
