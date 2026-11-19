# Homepage Editing Guide

This guide shows how to change the text and pictures on the website's front page
(homepage). You do not need to know any code — everything is edited with normal
forms in the WordPress admin area.

## Contents

1. [How to log in](#1-how-to-log-in)
2. [How the homepage is built](#2-how-the-homepage-is-built)
3. [Projects](#3-projects)
4. [Client Stories](#4-client-stories)
5. [Product Ranges](#5-product-ranges)
6. [Capabilities](#6-capabilities)
7. [Process Steps](#7-process-steps)
8. [Trust Facts](#8-trust-facts)
9. [FAQ](#9-faq)
10. [Sectors](#10-sectors)
11. [Materials & Features (project tags)](#11-materials--features-project-tags)
12. [Homepage Settings](#12-homepage-settings)
13. [Company info](#13-company-info)
14. [Where things appear on the homepage](#14-where-things-appear-on-the-homepage)
15. [Short answers to common questions](#15-short-answers-to-common-questions)

## 1. How to log in

1. Open the website's admin address in your browser:
   `https://mooblipoisid.ddev.site/wp-admin/`
   (for the live site, use the real address with `/wp-admin/` at the end).
2. Type your username and password.
3. Click **Log In**.
4. You now see the **dashboard** — the black menu on the left is where
   everything is edited.

> Tip: to see the website as a visitor sees it, click the house icon in the top
> bar (or open the site address in another tab). After you save a change,
> refresh that page to see it.

## 2. How the homepage is built

The homepage is **not** written inside a page. It is built automatically from
the content you enter in these admin screens:

- **Projects** — the project cards
- **Client Stories** — the customer quotes
- **Product Ranges** — the product lists
- **Capabilities** — what the workshop can make
- **Process Steps** — the numbered steps
- **Trust Facts** — the short facts under the big photo at the top
- **FAQ** — the questions and answers at the bottom of the homepage
- **Sectors** — the "who we build for" cards
- **Homepage Settings** (under **Settings**) — the top photo, headline, buttons
  and the two text bands

If you open the normal **Pages → Home** screen, you will only find a placeholder
note. Editing that page does **not** change the homepage. Use the screens listed
above instead.

**Adding something** always works the same way:

1. Click the item's name in the left menu (for example **Projects**).
2. Click **Add New** at the top.
3. Fill in the fields (each screen explains what to type).
4. Click the blue **Publish** button.

**Editing something:**

1. Click the item's name in the left menu.
2. Click the title of the item you want to change.
3. Change what you need, then click **Update**.

**Removing something (two safe options):**

- *Hide it for now:* open the item, find **Status** (top right, "Publish" box)
  and set it to **Draft**, then click **Update**. It disappears from the
  homepage but is not lost.
- *Delete it completely:* in the list, hover over the item's row and click
  **Trash**. It moves to the Trash and can be restored from there.

**Changing the order of items:**

Every list screen has an **Order** box (called "Order on Homepage" when you open
an item). Type a number: **lower numbers come first** (0 is first, then 1, 2,
3 …). Items with the same number keep their existing order. After changing an
order, click **Update**.

**Adding a picture:**

Wherever a section has a photo, the edit screen has a box with a
**Set card image** button (WordPress calls this the "featured image"):

1. Click **Set card image**.
2. Choose an existing picture, or click **Upload files** to add a new one.
3. Click **Set card image** in the corner of the window.
4. Click **Update** (or **Publish**) on the item.

## 3. Projects

**Admin menu:** **Projects** (left menu)

These are the cards in the "Recent Venue & Retail Projects" section.

| Field in the editor | What it shows on the homepage |
| --- | --- |
| **Project Name** (the title box) | The card heading |
| **Short Description (shown on the card)** | The card text under the heading |
| **Card Image** ("Set card image") | The card photo |
| **Location • Type** ("Entry Details" box) | The small grey line above the heading, e.g. "Tallinn, Estonia • Café & Roastery" |
| **Materials & Features** (the tag box) | The small tags on the card, e.g. "Smoked Oak" |
| **Sectors** | Which sector this project belongs to (Hospitality / Retail / Private). This groups your projects; it does not draw anything extra on this section |
| **Order** ("Order on Homepage" box) | Where the card appears in the row. Lower numbers first |

Steps to add a project:

1. Click **Projects → Add New**.
2. Type the project name in the big title box.
3. Type the card text in **Short Description (shown on the card)**.
4. In **Entry Details**, fill in **Location • Type** exactly as it should read,
   with the bullet: `Tallinn, Estonia • Café & Roastery`.
5. In the **Materials & Features** box, tick or add the materials used.
6. On the right, click **Set card image** and choose the photo.
7. In **Order on Homepage**, type the position (see below).
8. Click **Publish**.

Notes:

- The project with **Order 0** (the lowest number) is the big "lead" card at the
  start of the section. To make another project the lead card, give it the
  lowest order number and raise the others.
- The section heading "Recent Venue & Retail Projects" and the small label
  "Selected Commissions" are part of the page design and are not edited here.

## 4. Client Stories

**Admin menu:** **Client Stories** (left menu)

These are the quote cards in the "What Our Clients Say" section.

| Field in the editor | What it shows on the homepage |
| --- | --- |
| **The Client's Quote** (the main text box) | The quote itself — this is the main thing you type |
| **Name / Company** (the title box) | The name shown with the quote (keeps the entry easy to find in the list) |
| **Author Role** ("Entry Details" box) | The line under the quote, e.g. "Owner, Café & Roastery" |
| **City** ("Entry Details" box) | The place shown next to the person's details, e.g. "Tallinn" |
| **Star Rating** ("Entry Details" box) | The star row above the quote. Type 1–5, or leave empty for no stars |
| **Order** ("Order on Homepage") | The order of the quote cards. Lower numbers first |

Steps to add a quote:

1. Click **Client Stories → Add New**.
2. Type the person's name or company in the title box.
3. Type the quote in the main text box (**The Client's Quote**).
4. Fill in **Author Role**, **City** and **Star Rating** in **Entry Details**.
5. Set the **Order** if the position matters.
6. Click **Publish**.

## 5. Product Ranges

**Admin menu:** **Product Ranges** (left menu)

These are the lists in "The Full Commercial Range" section. The section has
**two columns**:

- the **first 6 items** (by order) form the left column — cafés, bars &
  restaurants;
- the **last 6 items** form the right column — retail shops.

| Field in the editor | What it shows on the homepage |
| --- | --- |
| **Range Name** (the title box) | One list entry, e.g. "Bars & counters" |
| **Short Description** | The short line under the entry name |
| **Card Image** | Available for the entry (the lists themselves show text only) |
| **Order** ("Order on Homepage") | The position in the list, and which column the entry lands in |

Steps:

1. Click **Product Ranges → Add New**.
2. Type the name and the short description.
3. Set the **Order** so the item sits in the right place (remember: first 6 =
   left column, last 6 = right column).
4. Click **Publish**.

> Important: the two column headings are fixed parts of the design. Keep 12
> items in total (6 + 6) so both columns stay balanced. If you remove one item,
> the remaining items simply split in half by order.

## 6. Capabilities

**Admin menu:** **Capabilities** (left menu)

These are the small cards in the "One Workshop. Every Material Your Venue Needs."
section (what we make in-house).

| Field in the editor | What it shows on the homepage |
| --- | --- |
| **Capability Name** (the title box) | The card heading |
| **Short Description** | The card text |
| **Order** ("Order on Homepage") | The card order. Lower numbers first |

Steps: click **Capabilities → Add New**, type the name and description, set the
**Order**, click **Publish**. (The small icons on these cards are part of the
design and follow the card's position.)

## 7. Process Steps

**Admin menu:** **Process Steps** (left menu)

These are the numbered steps in "From Brief to Opening Day".

| Field in the editor | What it shows on the homepage |
| --- | --- |
| **Step Name** (the title box) | The step heading |
| **Short Description** | The step text |
| **Week Label** ("Entry Details" box) | The time line at the bottom of the step, e.g. "Week 01 – 02" |
| **Order** ("Order on Homepage") | The step order — and therefore its number |

The step number (01, 02, 03 …) is added automatically from the step's position:
the first step by order becomes "01", the second "02", and so on. You do not type
the number yourself.

Steps: click **Process Steps → Add New**, fill in the three fields, make sure the
**Order** values run 0, 1, 2, 3 in the order you want the steps, click
**Publish**.

## 8. Trust Facts

**Admin menu:** **Trust Facts** (left menu)

These are the short facts in the strip right under the big photo at the top.

| Field in the editor | What it shows on the homepage |
| --- | --- |
| **Value (e.g. "18 Years")** (the title box) | The big bold value, e.g. "18 Years", "In-House Workshops" |
| **Caption (the line under the value)** | The smaller line under it, e.g. "Joinery & shopfitting experience" |
| **Order** ("Order on Homepage") | The order across the strip. Lower numbers first |

Steps: click **Trust Facts → Add New**, type the value and the caption, set the
**Order**, click **Publish**. Four facts fit the strip comfortably.

## 9. FAQ

**Admin menu:** **FAQ** (left menu)

These are the questions and answers in the "Frequently asked questions" section
near the bottom of the homepage. Every question has **its own version in each
language** — write them all so the section shows translated answers.

| Field in the editor | What it shows on the homepage |
| --- | --- |
| **Question** (the title box) | The question line the visitor clicks on |
| **Answer** (the main text box) | The answer shown when the question is opened |
| **Order** ("Order on Homepage") | The order of the questions. Lower numbers first |

Steps to add a question:

1. Click **FAQ → Add New**.
2. Type the question in the title box and the answer in the main text box
   (plain sentences — no headings or layout needed).
3. Set the **Order** (1, 2, 3 …).
4. Click **Publish**.

Translating a question:

1. Open the question in **FAQ**.
2. In the top admin bar, use the language switcher (the flags next to the
   language name) to create or open the Russian and English versions of the
   same question.
3. The three versions are linked: the homepage shows the one matching the
   language the visitor is reading.

Notes:

- The section heading ("Korduma kippuvad küsimused" etc.) is edited in
  **Settings → Homepage Settings** ("FAQ Section", see section 12).
- **If no questions exist in a language, the whole FAQ section hides itself on
  that language's homepage** — it never shows an empty box.
- The questions also feed the FAQ structured data (FAQPage JSON-LD) that
  search engines read; changing a question here updates that automatically.

## 10. Sectors

**Admin menu:** **Projects → Sectors** (left menu, under **Projects**)

Sectors are the "Who We Build For" cards (Hospitality, Retail, Private). They
also group your projects: each project can be given a sector.

| Field in the editor | What it shows on the homepage |
| --- | --- |
| **Name** | The sector name, e.g. "Hospitality" — shown as "Hospitality • 01" on the card |
| **Description** | The card text |
| **Sector Image** | The card photo — paste the web address of a picture (see below) |
| **Sort Order** | The card order and the number after "•": the first sector becomes "• 01", the second "• 02", and so on. Lower numbers first |

To add or edit a sector:

1. Click **Projects → Sectors**.
2. To change one, click its name. To add one, use the form on the left of the
   same screen.
3. Fill in the name, description, **Sector Image** and **Sort Order**.
4. Click **Update** (or **Add New Sector**).

To set the **Sector Image**:

1. Open **Media → Add New** and upload the photo.
2. Open the photo in the media library and copy the **File URL** (the image
   address shown on the right).
3. Paste that address into the **Sector Image** field of the sector.

Notes:

- The wording on the card buttons (for example "See the Hospitality Range") and
  the card headings are fixed parts of the design for the three existing
  sectors.
- After adding or removing a sector, check the numbering — the numbers follow
  the **Sort Order** automatically.

## 11. Materials & Features (project tags)

**Admin menu:** **Projects → Materials & Features**

These are the small tags shown on project cards, e.g. "Smoked Oak",
"Sintered Stone Top".

1. Click **Projects → Materials & Features**.
2. Type the name in **Add New Material / Feature** and click **Add New Material / Feature**.
3. On a project's edit screen, tick the tag in the **Materials & Features** box
   to show it on that card.

Tags appear on a card in the order you tick/add them to that project.

## 12. Homepage Settings

**Admin menu:** **Settings → Homepage Settings**

This screen holds the one-off wording at the top and bottom of the homepage.
Each field says what it controls. When finished, click **Save Homepage
Settings**.

**Hero (the top of the homepage)** — the large opening panel:

| Field | Shows |
| --- | --- |
| **Hero Headline** | The big sentence at the top |
| **Hero Subline** | The paragraph under the headline |
| **Primary Button Text** | The label of the main button, e.g. "Request a Quote" |
| **Primary Button Link** | Where that button goes. `#consultation` opens the form at the bottom of the homepage; a full web address works too |
| **Secondary Button Text** | The quieter second button, e.g. "See Commercial Projects" |
| **Secondary Button Link** | Where the second button goes, e.g. `#projects` |
| **Hero Image Address** | The large photo behind the text. Upload the photo under **Media**, open it, copy the **File URL** and paste it here |
| **Hero Image Description** | A short plain description of the photo (for screen readers and search engines) |

**Ethos Band** — the dark band in the middle of the page:

| Field | Shows |
| --- | --- |
| **Ethos Small Heading** | The small label above the quote, e.g. "Ethos & Provenance" |
| **Ethos Quote** | The large quote |
| **Ethos Body Text** | The paragraph under it |

**Consultation Band** — the introduction above the enquiry form:

| Field | Shows |
| --- | --- |
| **Consultation Small Heading** | The small label above the heading, e.g. "Start a Dialogue" |
| **Consultation Headline** | The heading above the form, e.g. "Let's plan your venue or store." |
| **Consultation Intro Text** | The paragraph under the heading, above the form |

**FAQ Section** — the heading of the questions section (the questions
themselves are managed under the **FAQ** menu, see section 9):

| Field | Shows |
| --- | --- |
| **FAQ Section Heading** | The heading above the questions, e.g. "Korduma kippuvad küsimused" |
| **FAQ Section Subline** | The short paragraph under the heading |

**SEO & Sharing** — how the homepage appears in search results and when it is
shared on social media. **Every field here is optional**: when left empty, the
site fills in sensible automatic values (the description falls back to the hero
subline, the share image falls back to the hero photo):

| Field | Shows |
| --- | --- |
| **SEO Meta Title** | The browser-tab and search-result title. Keep it around 60 characters |
| **SEO Meta Description** | The search-result snippet under the title. Keep it around 155 characters |
| **Social Sharing Image** | The image shown when someone shares the homepage link (ideally a wide picture, 1200 × 630 pixels) |

Notes:

- The enquiry form itself and the manager contact details (names, phone, email)
  are not edited here — ask the developer to change those.
- Use `#section-name` style links to point buttons at a place on the same page.

## 13. Company info

**Admin menu:** **Settings → Mööblipoisid: Company info**

This screen holds the company facts used by search engines and AI assistants —
it is **not** shown as visible text on the homepage. The values feed the
structured data (Organization JSON-LD) attached to every page and the
`/llms.txt` file that AI engines read.

| Field | Used as |
| --- | --- |
| **Legal Name** | The company name in structured data (e.g. "Mööblipoisid OÜ") |
| **Registry Code** | The official company identifier in structured data |
| **Phone / Alt Phone** | The phone number in structured data and `/llms.txt` |
| **Email / Alt Email** | The email address in structured data and `/llms.txt` |
| **Facebook / Instagram / LinkedIn** | Profile links in structured data (leave empty if none) |
| **Address (ET / RU / EN)** | Reserved for the business-address block — leave empty until the client confirms the exact wording |

Changes here are safe: nothing breaks if a field is empty, it is simply left
out of the structured data.

## 14. Where things appear on the homepage

| Homepage section (from the top) | Edited in |
| --- | --- |
| Hero: big photo, headline, two buttons | **Settings → Homepage Settings** (Hero) |
| Trust strip (facts under the hero) | **Trust Facts** |
| Sectors — "Furniture Made for Commercial Interiors" | **Projects → Sectors** |
| Product ranges — "The Full Commercial Range" | **Product Ranges** |
| Ethos band (dark quote band) | **Settings → Homepage Settings** (Ethos Band) |
| Projects — "Recent Venue & Retail Projects" | **Projects** (+ **Materials & Features** tags) |
| Capabilities — "One Workshop…" | **Capabilities** |
| Process — "From Brief to Opening Day" | **Process Steps** |
| Client stories — "What Our Clients Say" | **Client Stories** |
| FAQ — "Frequently asked questions" | **FAQ** (heading + subline in **Settings → Homepage Settings**, FAQ Section) |
| Consultation — intro above the enquiry form | **Settings → Homepage Settings** (Consultation Band) |
| Enquiry form and manager contacts | Fixed (ask the developer) |

## 15. Short answers to common questions

**Nothing changed after I clicked Update.**
Open the homepage in a private/incognito browser window (or press Ctrl+F5) —
your browser may be showing an older copy.

**How do I change the order of cards?**
Give each item a number in its **Order on Homepage** box (or **Sort Order** for
sectors). Lower numbers come first.

**How do I remove something without losing it?**
Set its status to **Draft** instead of deleting it.

**Where do I change the text on the buttons at the top?**
**Settings → Homepage Settings** — "Primary Button Text" and "Secondary Button
Text".

**Do I ever need to edit code or HTML?**
No. Everything on the homepage is editable from the screens in this guide. If
something looks like it cannot be changed here (the section headings, the
buttons on the sector cards, the enquiry form), it is part of the design — ask
the developer.
