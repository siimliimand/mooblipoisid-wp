# Mööblipoisid — WordPress Site

Website for **Mööblipoisid OÜ** (Reg. Nr 12340882, Tallinn, Estonia) — a custom furniture workshop specialising in commercial interiors: cafés, bars, restaurants and retail shops, plus bespoke private-sector kitchens.

## About the Business

Mööblipoisid designs, builds and installs custom furniture. Wood, metal and stone are all produced in-house (CNC woodworking, metalworking, stone fabrication), so complete packages arrive matching and ready to install.

### Core Products

**Hospitality (cafés, bars, restaurants)**
- Custom bar and service counters — often integrated with refrigeration, sinks and espresso stations
- Built-in banquette and booth seating
- Commercial-grade dining tables (scratch- and heat-resistant materials: compact laminate, sintered stone, treated hardwood)
- Barstools and chairs
- Waiter stations
- Acoustic wall paneling

**Retail shops**
- Point-of-sale (POS) checkout counters
- Modular display shelving
- Clothing racks
- Illuminated glass display cases
- Fitting room joinery
- Branded feature walls

**Private-sector kitchens**
- Bespoke kitchen cabinetry, kitchen islands, pantries and integrated appliance housing — built in the same CNC woodworking, metalworking and stone-fabrication workshops used for commercial bars

## Tech Stack

| Component   | Choice                                          |
|-------------|-------------------------------------------------|
| CMS         | WordPress 7.1.4 (committed in `public/`)        |
| Theme       | [Kadence](https://wordpress.org/themes/kadence/) 1.6.0 + `mooblipoisid-child` |
| Content     | Structured & admin-editable (custom post types + taxonomies + Homepage Settings) |
| Languages   | Estonian only for now (`et`); `ru`/`en` planned as a follow-up |
| Local dev   | DDEV (primary) or Docker (`docker-compose.yml`) |
| Production  | zone.ee (PHP hosting)                           |

### Languages

The production site will be trilingual:

- **et** — Estonian, the default locale (installed)
- **ru** — Russian (follow-up)
- **en** — English (follow-up)

Translations will be managed with a multilingual plugin (Polylang planned). All page content, menus and product/service descriptions must exist in all three languages. The homepage content is already structured into translatable custom post types and taxonomies, so the translation plugin can operate on real posts and terms rather than opaque markup.

## Local Development

Development runs in DDEV (Docker-based) so the local environment matches zone.ee's PHP + MySQL stack.

### Quick start (DDEV)

WordPress core is committed, so a fresh clone only needs DDEV and a database install:

```bash
# Install DDEV: https://docs.ddev.com/en/stable/
ddev start            # .ddev/config.yaml is committed; wp-config files are auto-generated

# First run only: install the database
ddev wp core install --url="https://mooblipoisid.ddev.site" --title="Mööblipoisid" \
  --admin_user=admin --admin_password="<choose-a-local-password>" \
  --admin_email=info@mooblipoisid.ee --skip-email
```

- Site: <https://mooblipoisid.ddev.site> · wp-admin: `/wp-admin/`
- DB credentials: `db` / `db` / `db` (DDEV defaults, managed by `wp-config-ddev.php`)
- Theme: Kadence + `mooblipoisid-child` are committed and pre-configured (palette, typography, header/footer builders are DB-stored; backups in `docs/kadence-theme-mods.json` and `docs/kadence-global-palette.json`)
- Outbound email is captured by DDEV's Mailpit: <https://mooblipoisid.ddev.site:8026> (enquiry form submissions land there)

### Quick start (plain Docker)

```bash
docker compose up -d
# WordPress at http://localhost:8080 (fresh install wizard)
```

Uses `wordpress:6.8-php8.3-apache` + MariaDB 11; the committed `wp-config-sample.php` documents the credential defaults (`db` host, `wordpress` DB).

## Deployment (zone.ee)

Production is hosted at [zone.ee](https://www.zone.ee/) on shared PHP hosting.

- Upload/deploy WordPress files over SFTP/SSH (`public/` contents → site docroot)
- Import the database and update `wp-config.php` with zone.ee DB credentials
- Set the site language to Estonian; install the multilingual plugin and add `ru` and `en`
- Zone serves sites over HTTPS by default (Let's Encrypt) — make sure site URL uses `https://`

## Repository Layout

```
.
├── README.md                       # this file
├── docker-compose.yml              # plain Docker fallback (WordPress + MariaDB)
├── .ddev/                          # DDEV project config (WordPress, PHP 8.3)
├── bin/                            # helper scripts (one-time content import)
├── public/                         # WordPress docroot (committed)
│   └── wp-content/themes/
│       ├── kadence/                # parent theme (do not edit)
│       └── mooblipoisid-child/     # all project code
│           ├── front-page.php      # renders the homepage from the content model
│           ├── template-parts/home/  # one template part per homepage section
│           ├── inc/content-model.php # CPT/taxonomy/Homepage Settings registration
│           ├── functions.php       # enquiry form + shortcodes
│           └── style.css           # design tokens + section styles
├── docs/
│   ├── html/                       # design prototype / static reference (not part of WP)
│   ├── EDITOR-GUIDE.md             # how to edit homepage content in wp-admin
│   ├── kadence-theme-mods.json     # customizer backup (recreate via wp eval-file if DB is lost)
│   ├── kadence-global-palette.json # global palette backup
│   └── homepage-content.html       # REFERENCE SNAPSHOT of the original static markup (not rendered)
└── openspec/                       # specs and change proposals (completed changes in changes/archive/)
```

### Homepage

The homepage is rendered dynamically by `front-page.php`, which pulls content from a structured, admin-editable content model — **not** from page block markup. Editors maintain it entirely in wp-admin (no code/HTML); see **`docs/EDITOR-GUIDE.md`**.

- **Custom post types:** `project` (featured project cards), `testimonial` (client stories), `range` (product ranges), `capability`, `step` (process steps), `trust_stat` (trust-strip facts). Each supports title, a short description, a card image and ordering.
- **Taxonomies:** `sector` (Hospitality / Retail / Private — drives the sectors section and tags projects) and `project_tag` (materials & features shown on project cards).
- **Homepage Settings** (Settings → Homepage Settings): the one-off static text — hero headline/sub/CTAs, ethos band, consultation intro.

Each homepage section is a template part in `template-parts/home/` that queries the matching content and emits the design's markup. `docs/homepage-content.html` is kept only as a reference snapshot of the original static version.

All post types and taxonomies are registered `public`, so a multilingual plugin (e.g. Polylang) will recognise them for translation. The enquiry form is the `[mooblipoisid_enquiry_form]` shortcode (rendered inside the `#consultation` section); submissions email both managers (verify deliverability with SMTP before launch).

## Contacts

- **Production Manager:** Andre Merila — andre@mooblipoisid.ee — +372 515 3229
- **Project Manager:** Marilyn Salu — marilyn@mooblipoisid.ee — +372 55 910 872
- Address: Võidu 75, 11212 Tallinn, Estonia
