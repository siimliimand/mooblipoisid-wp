# SEO & AI-visibility layer — status, seeds and launch checklist

Date: 2026-11-19

## 1. Audit summary (what was broken / missing)

Findings before the SEO layer was built, on `https://mooblipoisid.ddev.site`:

- **Wrong canonical on `/ru/` and `/en/`** — the canonical URL was built from
  the active language's `home_url()` while Polylang filters `home_url()` to the
  *current* language, so language roots compounded (`/ru/ru/` style) instead of
  pointing at each language's own root.
- **No meta titles or descriptions** — `<title>` was the raw Kadence fallback;
  no `meta name="description"` anywhere.
- **No Open Graph / Twitter Card tags** — shares had no title, description or
  image.
- **No structured data** — zero JSON-LD (no Organization, WebPage, FAQPage or
  product catalog), so nothing for search engines or AI engines to ground on.
- **No `robots.txt` control and no `llms.txt`** — nothing told crawlers or AI
  agents what the site is and which pages matter.
- **Double `<main>` element** — the page emitted two `<main>` landmarks
  (bad for accessibility and crawling); fixed to a single one.
- **`http://` links in flag URLs** — language-switcher flag markup used
  hardcoded `http://` URLs (mixed-content warnings on the HTTPS site).
- **External Google Fonts** — the homepage pulled
  `fonts.googleapis.com`/`fonts.gstatic.com` from the browser (GDPR + latency).
- **Junk posts** — the WP sample post "Tere-tere!" (#1) and a leftover
  "PARITY-TEST" post (#72) were still published.
- **Empty legal pages / no FAQ** — see section 3 for the content that was
  seeded afterwards.

## 2. What has been implemented

All of it ships with git — no DB-only hacks:

- **`public/wp-content/mu-plugins/mooblipoisid-seo.php`** (mu-plugin, always
  on, cannot be deactivated from the admin):
  - Document `<title>` overrides per language; meta descriptions.
  - Open Graph + Twitter Card tags (title, description, image).
  - Canonical URLs + `x-default` hreflang, per-language alternates — the
    language-root canonical bug is fixed via the child theme's
    `mooblipoisid_polylang_canonical_home_url()` helper (Polylang links model).
  - Head-noise removal (RSD, wlwmanifest, emoji scripts, shortlinks, …).
  - One JSON-LD `@graph`: **Organization** (name, logo, url, email, telephone,
    `identifier` = registry code, `contactPoint`, `sameAs` when socials are
    filled in, `hasOfferCatalog` from the Product Ranges), **WebSite**,
    **WebPage**, **BreadcrumbList** (non-front singulars) and **FAQPage**
    (whenever the FAQ content has items in the active language).
  - `/llms.txt` route: human/agent-readable summary with a Contact block
    (phone/email/registry code from Company info), Products list and Links to
    every published page in all three languages.
  - Sitemap trimmed: the `users` provider is removed
    (`wp-sitemap.xml` has no author URLs).
  - One-time rewrite flush so `/llms.txt` resolves after deploy.
- **Page-level SEO meta on Pages**: `_mooblipoisid_seo_title`,
  `_mooblipoisid_seo_description`, `_mooblipoisid_seo_social_image` post meta
  (SEO metabox on the page edit screen).
- **Homepage FAQ system** (child theme `content-model.php` +
  `template-parts/home/faq.php`): `mooblipoisid_faq` post type (question =
  title, answer = editor, ordered by "Order on Homepage"), Polylang
  translatability enabled, section auto-hides when a language has no items,
  FAQPage JSON-LD mirrors it automatically.
- **New Homepage Settings sections**: **FAQ** (`faq_eyebrow`, `faq_heading`,
  `faq_sub`) and **SEO & Sharing** (`seo_meta_title`, `seo_meta_description`,
  `seo_social_image`) — optional overrides; smart fallbacks exist (title falls
  back to a branded default, description falls back to the hero subline, social
  image falls back to the hero image).
- **Settings → "Mööblipoisid: Company info"**: single option
  (`mooblipoisid_company_info`) feeding the Organization JSON-LD and the
  llms.txt Contact block.
- **Kadence local Google fonts** enabled (see section 5).
- **Seeders** (idempotent, `ddev wp eval-file bin/…php`):
  - `bin/seed-legal-pages.php` — the three legal pages × 3 languages.
  - `bin/seed-homepage-settings.php` — per-language homepage copy.
  - `bin/seed-faq.php` — the six FAQ items × 3 languages (created 18 posts,
    translations linked; second run creates nothing).
- **Trashed junk posts** #1 and #72 (recoverable from Trash, not deleted).
- **WordPress sample page** (`demoleht`) trashed by the legal seeder.

## 3. DRAFTS FOR CLIENT REVIEW ⚠️

Everything in this section was **seeded by the developer, not written by the
client**. It is live on the site already — please review and correct wording
before launch. All of it is editable in wp-admin (no code).

### 3.1 FAQ questions & answers (homepage FAQ section)

Edit under **wp-admin → FAQ** (question in the title field, answer in the text
box; "Order on Homepage" controls the order). Estonian is the anchor entry; RU
and EN are its translations (switch via the language switcher in the admin bar).

**1. Kuidas toimib eritellimusel mööbli tellimine?** *(order: 1)*
- **ET:** Iga projekt algab briefist ja ruumiga tutvumisest, millele järgneb
  kontseptsioon ja pakkumine koos materjalivaliku, hinna ja tähtajaga. Pärast
  tellimuse kirjalikku kinnitamist koos ettemaksuga valmistame mööbli oma
  töökojas Tallinnas. Viimane samm on paigaldus ja üleandmine koos
  hooldusjuhendiga.
- **RU:** Как проходит заказ мебели на заказ? — Каждый проект начинается с
  брифа и знакомства с помещением, затем следуют концепция и предложение с
  выбором материалов, ценой и сроком. После письменного подтверждения заказа с
  предоплатой мы изготавливаем мебель в собственной мастерской в Таллине.
  Последний этап — монтаж и сдача вместе с инструкцией по уходу.
- **EN:** How does ordering custom furniture work? — Every project starts with
  a brief and a look at the space, followed by a concept and an offer with
  material choices, price and lead time. After the order is confirmed in
  writing with a deposit, we build the furniture in our own workshop in
  Tallinn. The final step is installation and handover, including care
  instructions.

**2. Kui kaua võtab eritellimuse valmistamine aega?** *(order: 2)*
- **ET:** Tüüpiline valmimisaeg on 4–8 nädalat alates tellimuse kirjalikust
  kinnitamisest, ettemakse laekumisest ja materjalivaliku heakskiitmisest.
  Tähtaegu võivad mõjutada materjalide kättesaadavus ja kliendipoolse
  tagasiside tempo — hoiame teid edenemisest kogu projekti vältel kursi.
  Keerukamad lahendused ja erivajadused kokkuleppel.
- **RU:** Сколько времени занимает изготовление заказа? — Обычный срок
  изготовления — 4–8 недель с момента письменного подтверждения заказа,
  поступления предоплаты и утверждения выбора материалов. На сроки могут
  влиять доступность материалов и скорость обратной связи со стороны клиента —
  мы держим вас в курсе на протяжении всего проекта. Более сложные решения и
  особые требования — по договорённости.
- **EN:** How long does a custom order take? — A typical lead time is 4–8
  weeks from written order confirmation, receipt of the deposit and approval
  of the material selection. Lead times may be affected by material
  availability and the pace of client feedback — we keep you posted throughout
  the project. More complex solutions and special requirements are agreed
  individually.

**3. Mis materjalidest te mööblit valmistate?** *(order: 3)*
- **ET:** Kasutame tahaspuitu ja plaadimaterjale vastutustundlikult
  majandatud metsadest — võimalusel FSC- või PEFC-sertifitseeritud
  allikatest. Meie põhimaterjalid on Põhja- ja Baltimaade tamm, saar, kask ja
  mänd; hinged, suuliskapid ja tõmme pärinevad tunnustatud Euroopa tootjatelt.
  Materjalide päritolu kohta loe lähemalt lehelt „Materjalide päritolu“.
- **RU:** Из каких материалов вы изготавливаете мебель? — Мы используем массив
  дерева и плитные материалы из лесов с ответственным хозяйствованием — по
  возможности из FSC- или PEFC-сертифицированных источников. Наши основные
  породы — северный и балтийский дуб, ясень, берёза и сосна; петли,
  направляющие и ручки поставляют признанные европейские производители.
  Подробнее о происхождении материалов — на странице «Происхождение
  материалов».
- **EN:** What materials do you build with? — We use solid wood and panel
  materials from responsibly managed forests — FSC- or PEFC-certified sources
  wherever possible. Our core species are Nordic and Baltic oak, ash, birch
  and pine, and hinges, drawer systems and pulls come from established
  European manufacturers. You can read more on the Material Provenance page.

**4. Kas saate töötada meie arhitekti jooniste järgi?** *(order: 4)*
- **ET:** Jah — valmistame mööbli teie jooniste järgi või koostöös teie
  arhitektiga. Töötame teie siseviimistluse graafiku järgi, koordineerime teie
  arhitekti ja töövõtjatega ning paigaldame teie avamiskuupäeva ümber. Kuna
  kogu tootmine käib meie enda töökojas, saabuvad osad viimistletuna ja
  paigaldusvalmisena.
- **RU:** Можете ли вы работать по чертежам нашего архитектора? — Да — мы
  изготавливаем мебель по вашим чертежам или в сотрудничестве с вашим
  архитектором. Мы работаем по вашему графику отделки, координируем действия
  с архитектором и подрядчиками и монтируем с учётом вашей даты открытия.
  Поскольку всё производство ведётся в нашей собственной мастерской, детали
  приходят отделанными и готовыми к монтажу.
- **EN:** Can you work from our architect's drawings? — Yes — we build from
  your drawings or in cooperation with your architect. We work to your
  fit-out programme, coordinate with your architect and contractors, and
  install around your opening date. Because everything is produced in our own
  workshop, parts arrive finished and ready to install.

**5. Kellele te mööblit valmistate?** *(order: 5)*
- **ET:** Valmistame mööblit ja sisustuslahendusi kohvikutele, baaridele ja
  restoranidele ning jaekauplustele ja kaubanduslikele interjöörile. Lisaks
  teeme erilahendusega erakööke. Kõik esemed on kavandatud pika ja pideva
  kasutuse jaoks.
- **RU:** Для кого вы изготавливаете мебель? — Мы изготавливаем мебель и
  интерьерные решения для кофеен, баров и ресторанов, а также для магазинов и
  торговых интерьеров. Кроме того, мы делаем частные кухни по индивидуальным
  решениям. Все изделия рассчитаны на долгую и интенсивную эксплуатацию.
- **EN:** Who do you build furniture for? — We make furniture and interior
  solutions for cafés, bars and restaurants, as well as retail stores and
  commercial interiors. We also build bespoke private kitchens. Every piece is
  designed for long hours of constant use.

**6. Kuidas päringut esitada ja kui kiiresti vastate?** *(order: 6)*
- **ET:** Saadake meile oma plaanid, joonised või kontseptsioonitahvlid koos
  mõõtude ja materjalisoovidega — päringuvorm on meie veebilehel. Vaatame
  teie briefi üle ja vastame esialgse tagasisidega 48 tunni jooksul. Töökoja
  ja näidiste külastus Tallinnas on võimalik kokkuleppel.
- **RU:** Как отправить запрос и как быстро вы отвечаете? — Пришлите нам свои
  планы, чертежи или концепт-борды с размерами и пожеланиями по материалам —
  форма запроса есть на нашем сайте. Мы рассмотрим ваш бриф и ответим с
  первичной обратной связью в течение 48 часов. Посещение мастерской и
  знакомство с образцами в Таллине возможно по предварительной договорённости.
- **EN:** How do I send an enquiry and how quickly do you respond? — Send us
  your floor plans, drawings or concept boards with dimensions and material
  preferences — there is an enquiry form on our website. We will review your
  brief and respond with initial feedback within 48 hours. Workshop and sample
  visits in Tallinn are possible by appointment.

> Every answer above is grounded in the site's own copy (commission terms,
> material provenance page, homepage process/consultation text). Nothing was
> invented — no prices, no delivery guarantees, no warranty promises.

### 3.2 Homepage FAQ section headings

Edit under **Settings → Homepage Settings → "FAQ Section"**:

| Field | et | ru | en |
| --- | --- | --- | --- |
| Eyebrow | Korduma kippuvad | Частые вопросы | Frequently asked |
| Heading | Korduma kippuvad küsimused | Часто задаваемые вопросы | Frequently asked questions |
| Subline | Kõige levinumad küsimused eritellimusel mööbli kohta — tellimuse käigust, tähtaegadest ja materjalidest. | Самые частые вопросы о мебели на заказ — о ходе заказа, сроках и материалах. | The most common questions about custom furniture — the order process, lead times and materials. |

### 3.3 Homepage SEO meta titles (browser tab / search result)

Edit under **Settings → Homepage Settings → "SEO & Sharing"**:

| Language | seo_meta_title |
| --- | --- |
| et | Mööblipoisid — äriruumide mööbel ja sisustus |
| ru | Mööblipoisid — мебель и интерьеры для бизнеса |
| en | Mööblipoisid — furniture & interiors for commercial spaces |

Meta **descriptions** and the social sharing image were intentionally left
empty: the site falls back to the hero subline / hero image. Fill them in when
the client approves final wording.

### 3.4 Company info

Edit under **Settings → Mööblipoisid: Company info**. Values were taken from
the site footer / existing copy (not invented):

| Field | Value |
| --- | --- |
| Legal name | Mööblipoisid OÜ |
| Registry code | 12340882 |
| Phone | +372 515 3229 |
| Alt phone | +372 55 910 872 |
| Email | marilyn@mooblipoisid.ee |
| Alt email | andre@mooblipoisid.ee |
| Facebook / Instagram / LinkedIn | empty (no verified profiles yet) |
| Addresses (et/ru/en) | empty (feeds `LocalBusiness` later — see section 6) |

## 4. Launch checklist (production deploy)

### 4.1 Deploy notes

- The mu-plugin (`wp-content/mu-plugins/mooblipoisid-seo.php`) and
  `robots.txt` ship with git — nothing to configure by hand.
- **No new `wp-config.php` constants were introduced by the SEO layer** (the
  email CPaaS constants from `docs/email-cpaas-setup.md` are the only ones and
  remain gitignored).
- **Local Google fonts may need regeneration on production**: font files are
  cached in `wp-content/fonts/` and are **not** in git. On the first frontend
  render after deploy, Kadence downloads and stores them automatically — this
  needs outbound HTTPS from the web server (zone.ee allows it). After deploy,
  verify with the curl checks in 4.3; if the local stylesheet is missing or
  the page still references `fonts.googleapis.com`, open
  **Appearance → Customize → General → Performance → Load Google Fonts Locally**
  (must be ON) and click **Flush Local Font Files**, then reload the homepage.
  The `wp-content/fonts/` folder is safe to delete anytime — Kadence rebuilds
  it (a weekly WP-Cron job cleans it automatically).
- After deploy, visit `/` once logged out — the one-time rewrite flush in the
  mu-plugin registers the `/llms.txt` rewrite.

### 4.2 Search Console + Bing Webmaster

1. **Google Search Console** (<https://search.google.com/search-console>):
   add a **Domain** property for `mooblipoisid.ee`, verify via the DNS TXT
   record at Zone (domain DNS is at Zone — same as the SPF/DKIM rows in
   `docs/email-cpaas-setup.md`).
2. Submit three sitemaps: `https://mooblipoisid.ee/wp-sitemap.xml`,
   `https://mooblipoisid.ee/ru/wp-sitemap.xml` (if Polylang serves per-language
   sitemaps — otherwise the root one covers all languages),
   and re-check "Pages" coverage after a week.
3. **Bing Webmaster Tools** (<https://www.bing.com/webmasters>): import the
   site from Search Console (fastest), confirm verification, submit the same
   sitemap(s).
4. Optionally request indexing for `/`, `/ru/`, `/en/` in both tools after
   launch.

### 4.3 Re-verification commands (run after deploy)

```bash
# Homepage: FAQ section present, one canonical per language, seeded titles
curl -sk https://mooblipoisid.ee/ | grep -c 'mp-faq__item'        # expect 6
curl -sk https://mooblipoisid.ee/ | grep -o '<title>[^<]*</title>'
curl -sk https://mooblipoisid.ee/ | grep -o '<link rel="canonical"[^>]*>'  # exactly 1
curl -sk https://mooblipoisid.ee/ru/ | grep -o '<link rel="canonical"[^>]*>' # /ru/ root, not /ru/ru/
curl -sk https://mooblipoisid.ee/en/ | grep -o '<link rel="canonical"[^>]*>' # /en/ root

# JSON-LD: exactly one ld+json block; parse the types (expect Organization, WebSite, WebPage, FAQPage)
curl -sk https://mooblipoisid.ee/ | grep -c 'application/ld+json'  # expect 1

# Fonts: no external Google Fonts, local stylesheet instead
curl -sk https://mooblipoisid.ee/ | grep -c 'fonts.googleapis.com\|fonts.gstatic.com'  # expect 0
curl -sk https://mooblipoisid.ee/ | grep -o 'wp-content/fonts/[a-z0-9]*\.css' | head -1

# Routes + sitemap
curl -sk -o /dev/null -w '%{http_code}\n' https://mooblipoisid.ee/llms.txt    # 200
curl -sk -o /dev/null -w '%{http_code}\n' https://mooblipoisid.ee/robots.txt  # 200
curl -sk https://mooblipoisid.ee/wp-sitemap.xml | grep -c '<sitemapindex' || true  # no users provider

# CLI equivalents (needs wp-cli on the server)
wp post list --post_type=post --post_status=publish    # expect empty (junk posts trashed)
wp eval 'do_action("init"); var_dump( PLL()->model->is_translated_post_type("mooblipoisid_faq") );'  # true
wp eval 'do_action("init"); print_r( count( mooblipoisid_get_faqs() ) );'  # 6
```

No PHP warnings should appear in any of the responses (check the raw output,
not just grep results).

## 5. Local Google Fonts — how it was enabled

Kadence 1.6 gates local font hosting behind the theme options
`load_fonts_local` (Customizer: **General → Performance → Load Google Fonts
Locally**) and `preload_fonts_local` (**Preload Local Fonts**). They are plain
theme mods, so they were enabled programmatically:

```bash
ddev wp eval 'set_theme_mod("load_fonts_local", true); set_theme_mod("preload_fonts_local", true);'
```

No Customizer click is required — when `load_fonts_local` is on, Kadence's
`get_webfont_url()` (the WPTT WebFont Loader) downloads the Google Fonts CSS
and the woff2 files on the next frontend render and serves them from
`wp-content/fonts/` (`<link rel="stylesheet" id="kadence-fonts-gfonts-css"
href="…/wp-content/fonts/<hash>.css">` + webfont preloads). A "Flush Local
Font Files" button in the same Customizer section (and a weekly WP-Cron job)
clears the cache; the files rebuild automatically on the next page load.

Verified on DDEV: `fonts.googleapis.com`/`fonts.gstatic.com` references went
from present to **0** on `/`, `/ru/` and `/en/`; the local stylesheet and the
woff2 files (Playfair Display, Plus Jakarta Sans) return HTTP 200.

## 6. Future / optional items

- **`LocalBusiness` upgrade** — the Organization JSON-LD can become
  `LocalBusiness` (or `FurnitureStore`) once a public street address is
  approved: fill the **address_et / address_ru / address_en** fields in
  Settings → Mööblipoisid: Company info, then extend the mu-plugin's
  Organization node with `address` + `openingHours` (client must confirm the
  opening hours first).
- **Social profiles (`sameAs`)** — fill facebook/instagram/linkedin in Company
  info and the mu-plugin emits `sameAs` automatically; needs the real profile
  URLs from the client.
- **Meta descriptions + social image** — currently smart-fallback
  (hero subline / hero image). Write per-language descriptions (~155 chars)
  and upload a 1200×630 share image once wording is approved (section 3).
- **JSON-LD `Organization.logo`** — swap the placeholder logo for the final
  brand logo when the photography/logo round lands.
- **Search Console monitoring** — check "Pages" and "Enhancements" reports
  1–2 weeks after launch; FAQ rich results may take time to appear and Google
  does not guarantee them.
