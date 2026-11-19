# Outgoing email — Zoho CPaaS setup (PAUSED, resume after DNS records are added)

Date paused: 2026-11-19

## What's already in place (done)

- **`wp-content/mu-plugins/mooblipoisid-cpaas-mail.php`** — overrides the
  pluggable `wp_mail()`: sends everything through the Zoho CPaaS
  (formerly ZeptoMail) REST API (`POST /v1.1/email`). Lives in mu-plugins,
  loads before pluggable.php, can't be deactivated from the admin.
  - Handles to/cc/bcc/reply-to (dynamic reply-to works via API), HTML/plain
    bodies, attachments (base64, ~10 MB cap), UTF-8, all core wp_mail shapes.
  - Empty API key = plain PHP `mail()` fallback (Mailpit on DDEV).
- **Enquiry form** stores uploads in `uploads/mp-enquiries/` and emails
  public links (not attachments); daily WP-Cron cleanup deletes files after
  30 days (`mooblipoisid_enquiry_retention_days` filter). Tested end-to-end.
- **`wp-config.php` constants** (gitignored — replicate manually on production):
  ```php
  define( 'MOOBLIPOISID_CPAAS_API_KEY', 'wSsVR…' );        // Send Mail token (agent 670a513f522d0074), LIVE locally
  define( 'MOOBLIPOISID_CPAAS_ENDPOINT', 'https://cpaas.zoho.com/v1.1/email' ); // US DC (their Zoho Mail is on mx.zoho.com)
  define( 'MOOBLIPOISID_MAIL_FROM', 'marilyn@mooblipoisid.ee' );
  define( 'MOOBLIPOISID_MAIL_FROM_NAME', 'Mööblipoisid' );
  ```

## Test state when paused

- API auth ✓, domain ✓, payload schema ✓ (to/cc/bcc nest under `email_address`)
- **Blocker: `429 TM_5001 Credit exhausted`** — account has no email credits.
  Console → Billing → claim the free first credit (10k emails) or buy one ($2.50).
- Until credits exist, local form submissions fail at the send step
  (`?enquiry=error`). Temporarily emptying the API key switches back to the
  Mailpit fallback if local testing is needed meanwhile.
- SMTP relay (`smtp.zeptomail.com`, ports 465/587) tested: connection+TLS fine,
  auth fails `535` with BOTH credential pairs (API token AND the
  `emailappsmtp.65c618c078e538e1` / `VHGuFqevGtKG` pair). Likely account-level
  gating until credits/billing. Not needed by the website (API path is used) —
  only for external tools. SMTP message limit ~51 MB vs API ~10 MB.

## Resume checklist (after DNS rows are added at Zone)

1. Add credits in CPaaS console (free 10k or $2.50/10k).
2. Verify `mooblipoisid.ee` in the CPaaS console → it prints **SPF + DKIM TXT
   records** → add them at **Zone** (domain DNS is at Zone; registrar is
   Radicenter OÜ — split setup, DNS edits go to Zone regardless).
3. SPF must stay ONE line — combine Zoho Mail + CPaaS includes, e.g.
   `v=spf1 include:zohomail.com include:transmail.net ~all`
   (use the exact includes both consoles show). DKIM record from the console.
4. Re-run tests: `ddev wp eval-file` smoke send via `mooblipoisid_cpaas_send()`
   → expect HTTP 200; then submit the real form with a file → check
   marilyn@mooblipoisid.ee inbox (and spam folder) for the link email.
5. Re-test SMTP relay (optional; expect auth to work once the account is
   activated — if still `535`, generate a fresh SMTP credential in the console).
6. On production deploy: copy the four `MOOBLIPOISID_*` constants into the
   production `wp-config.php` (they are gitignored by design).
7. Optional cleanup: consider moving the registrar to Zone (expiry 2027-08-06)
   so domain + DNS + hosting share one login; and ask the client why the
   domain was moved to Radicenter on 2026-10-06.
