# ReliefFlow

A humanitarian logistics coordination platform connecting **relief warehouses**, **depot managers**, and **field coordinators** to track relief item stock, route aid requests, and confirm deliveries with a QR-verified dispatch manifest — with AI-assisted triage, warehouse matching, and delivery verification throughout. Built with **Laravel 12**, fully **bilingual (Arabic/English)** with automatic RTL/LTR switching, and a public GPS map plus a no-login shipment tracker.

---

## 1. How it works

Every field request moves through a clear state machine on the `aid_requests` table:

```
pending  →  dispatched  →  delivered
    ↓
 rejected (a depot manager or admin can decline a request that can't be fulfilled)
```

1. A **field coordinator** submits an aid request: a target distribution location (optionally pinned on a map), notes, and one or more relief items with quantities. An AI pass reads the location and notes and tags the request `normal` / `high` / `critical` priority for triage — advisory only, it never blocks submission.
2. A **depot manager** (or admin) opens the request and sees every active warehouse **ranked by distance and by whether it currently holds enough stock for every requested item**. They dispatch straight from the best match, or reject the request with a reason if nothing can fulfill it. Dispatch re-checks stock for every item before committing; if anything is short, nothing is deducted and the exact shortfall is shown.
3. On a successful dispatch, stock is deducted from that warehouse, a **shipment** record is created with a unique QR tracking token, and a printable manifest becomes available — plus a public, no-login tracking page anyone can reach by scanning the QR code.
4. The coordinator confirms receipt in the field once the goods arrive, optionally attaching a delivery photo. If attached, an AI pass does a quick plausibility check against the manifest and flags the delivery **AI Verified** or **Needs Review** (advisory, not a hard gate) — closing out both the shipment and the aid request as **delivered**.

---

## 2. Roles

| Role | Value | How the account is created | Starting status |
|---|---|---|---|
| Administrator | `admin` | **Only** via `php artisan app:create-admin` — no public signup | `active` |
| Depot Manager | `depot_manager` | Public registration | `pending_verification` (needs admin approval) |
| Field Coordinator | `coordinator` | Public registration | `pending_verification` (needs admin approval) |

Account status: `pending_verification` / `active` / `suspended`. A pending or suspended user is redirected to an explanatory waiting page instead of the dashboard.

**Admin:** manages warehouses and relief items, approves/suspends accounts, and can also reject/dispatch requests and confirm deliveries — full visibility across the platform, plus the AI impact report.

**Depot Manager:** manages warehouse stock levels, reviews pending aid requests using AI-ranked warehouse matches, dispatches or rejects them, and tracks shipments already on the road.

**Field Coordinator:** submits aid requests with any number of relief items, tracks their own requests, and confirms delivery once a shipment reaches them.

---

## 3. AI features

All four run through a single `AIService`, wired to OpenAI's API. **Every one has a safe simulation-mode fallback** when `OPENAI_API_KEY` is left blank in `.env` — the app runs and demos fully with zero external calls and zero cost; connecting a key only makes the results smarter, nothing depends on it being present.

- **Priority triage** — every new aid request is classified `normal` / `high` / `critical` from its notes and location. Simulation mode uses a keyword heuristic (urgent, emergency, injured, medical, children...).
- **Smart warehouse matching** — `LogisticsService` ranks every active warehouse for a pending request by real Haversine distance and by whether it can fully cover every requested item, so the depot manager dispatches from the best option in one click instead of guessing.
- **Delivery photo verification** — an optional photo attached at delivery is checked against the shipment's manifest and flagged `AI Verified` or `Needs Review`. Simulation mode always returns `Needs Review` with a note to check manually — it never fabricates a "verified" result without a real model behind it.
- **Humanitarian impact report** — an AI-written 3–4 sentence narrative summary built strictly from real platform statistics (deliveries, active shipments, pending/rejected counts, categories distributed), available to admins and depot managers, printable.

---

## 4. Notifications

Every meaningful status change fires a Laravel Notification on both the `database` channel (the bell icon in the sidebar, with an unread badge and mark-as-read) and the `mail` channel where the recipient needs to act on it:

| Event | Recipient(s) | Channels |
|---|---|---|
| Account approved / suspended | The affected user | database + mail |
| Aid request submitted | Every active admin and depot manager | database |
| Aid request rejected | The coordinator who submitted it | database + mail |
| Shipment dispatched | The coordinator who submitted the request | database + mail, plus an SMS + WhatsApp alert to the **driver** (who has no account — reached by phone number only) |
| Shipment delivered | Every active admin and depot manager | database |

Driver SMS/WhatsApp alerts go through `NotificationService`, wired to Twilio and UltraMsg. Leaving `TWILIO_SID`/`TWILIO_AUTH_TOKEN`/`TWILIO_NUMBER` or `ULTRAMSG_INSTANCE_ID`/`ULTRAMSG_TOKEN` blank in `.env` logs the message instead of sending it — the dispatch flow works fully without either service configured. Mail follows whatever `MAIL_MAILER` is set to (`log` by default, so nothing is actually sent until a real driver is configured).

---

## 5. Password reset

Standard "forgot password" flow: `/forgot-password` accepts an email, and — regardless of whether that email is registered, so the page never reveals which accounts exist — replies with the same generic success message. If it is registered, a signed, time-limited reset link is emailed via `ResetPasswordNotification` (mail-only channel) using Laravel's built-in `password_reset_tokens` table and `Password` broker. Following the link opens `/reset-password/{token}`, and submitting a new password there resets it and redirects to login with a success flash. No third-party package involved — it reuses the same hand-rolled auth style and themed mail as the rest of the app.

---

## 6. Audit trail

Every aid request keeps a full activity timeline, not just its current status: who submitted it, who rejected or dispatched it (and why/from where), and who confirmed delivery, each with a timestamp. It's logged automatically by `AidRequestController` and `ShipmentController` into the `aid_request_activities` table and rendered as a chronological log on the request's detail page — useful for accountability when multiple staff touch the same request over its lifecycle.

---

## 7. Bilingual support

Every page renders in Arabic or English based on the session locale, switchable from the sidebar/login screen/landing page at any time. The `<html dir>` attribute flips automatically between `rtl` and `ltr`, and the layout uses CSS logical properties (`ms-`, `me-`, `ps-`, `pe-`, `text-start`/`text-end`) so spacing and alignment mirror correctly in both directions without duplicated markup. Translations live in `lang/ar.json` (English strings are the translation keys, so English needs no separate file).

---

## 8. Pages

Public (no login): landing page, login, register, forgot/reset password, `/track/{token}` shipment tracker (status, manifest, driver name — deliberately omits driver phone and exact warehouse coordinates).

Authenticated: role-aware dashboard, Warehouses (list + detail with GPS map, admin-managed), Relief Items (admin-managed), Inventory overview, Aid Requests (list + detail with activity log/audit trail and AI-ranked dispatch), Shipments (detail with delivery confirmation + AI verification), Map (all warehouses and open requests plotted together), Impact Report (admin/depot manager), Accounts (admin), Profile, Help (role-aware FAQ).

---

## 9. Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Auth | Session-based, hand-rolled registration + approval flow (no third-party auth package) |
| Database | SQLite for local development (swap via `.env` for MySQL/PostgreSQL in production) |
| Frontend | Blade + Alpine.js + Tailwind CSS v4 |
| Fonts | IBM Plex Sans / IBM Plex Sans Arabic via `@fontsource`, bundled through Vite — no Google Fonts CDN call |
| Maps | Leaflet.js + OpenStreetMap tiles, Leaflet itself bundled locally via npm/Vite (only the map tile images are fetched live, same as any web map) |
| Charts | Chart.js, bundled locally via npm/Vite |
| Icons | Inline SVG icon set (`<x-icon>`), no external icon font/request |
| AI | OpenAI Chat Completions API via `AIService`, simulation mode when no key is set |
| Notifications | Laravel Notifications (database + mail), Twilio (SMS) and UltraMsg (WhatsApp) for driver alerts, simulation mode when unconfigured |
| Build | Vite |
| QR codes | `bacon/bacon-qr-code` — generated locally as inline SVG, no external API call |
| Tests | PHPUnit (Feature tests) |

---

## 10. Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate
php artisan storage:link

npm install
npm run build   # or: npm run dev

php artisan serve
```

### Create the administrator account

There is no public signup for admins — create the first (and any further) admin from the terminal:

```bash
php artisan app:create-admin
```

### Enabling real AI responses (optional)

Set `OPENAI_API_KEY` in `.env`. Without it every AI feature above still works end-to-end in simulation mode.

### Demo data

`php artisan db:seed` creates a depot manager and a field coordinator (`manager@reliefflow.com` / `coordinator@reliefflow.com`, password `password`), three sample warehouses with real GPS coordinates, four relief items, some starting stock, and one sample critical-priority aid request — useful for trying the full flow, including the map and smart matching, immediately after `app:create-admin`.

---

## 11. Tests

```bash
php artisan test
```

Feature tests cover: registration and admin approval (including that pending/suspended accounts can't reach the dashboard and nobody can self-register as admin), the full aid-request lifecycle (multi-item requests, AI priority tagging, dispatch with stock deduction, insufficient-stock rejection, request rejection, delivery confirmation and who's allowed to confirm it), admin-only warehouse/item/inventory management (including that a warehouse with shipment history or an item with stock can't be deleted), the public tracking page (and that it never leaks driver phone numbers), profile/password updates, the AI/logistics services directly (priority classification, warehouse ranking by distance and stock, delivery photo verification) in simulation mode, and notifications (who gets notified for each event, that suspended staff are excluded, and that the bell's mark-as-read flow works).

---

## 12. Project structure

```
app/
  Http/Controllers/
    AuthController.php            # Login / logout
    Auth/RegisteredUserController.php
    Auth/PasswordResetController.php  # forgot/reset password
    AdminController.php           # Accounts page, approve / suspend
    DashboardController.php       # Per-role dashboard data
    WarehouseController.php       # index/show + admin-only CRUD
    ItemController.php            # index + admin-only CRUD
    InventoryController.php       # Stock overview + add stock
    AidRequestController.php      # index/create/show/store/reject/dispatch
    ShipmentController.php        # show/deliver/print + public track()
    ProfileController.php
    ReportController.php          # AI impact report
    MapController.php             # GPS overview
  Policies/
    AidRequestPolicy.php          # create / view / reject / dispatch / confirmDelivery
    ShipmentPolicy.php            # view / deliver
  Http/Middleware/
    EnsureUserIsAdmin.php
    EnsureUserIsActive.php        # Gates the dashboard for pending/suspended accounts
    SetLocale.php
  Services/
    AIService.php                 # OpenAI integration + simulation-mode fallback
    LogisticsService.php          # Haversine distance + warehouse stock ranking
    QrCodeService.php             # Local SVG QR generation
    NotificationService.php       # Twilio SMS + UltraMsg WhatsApp, simulation-mode fallback
  Notifications/                  # One class per event — see the Notifications table above
                                   # + ResetPasswordNotification.php
  Mail/ReliefFlowAlertMail.php    # Shared HTML mail template, escapes all interpolated content
  Models/AidRequestActivity.php   # One row per audit-trail entry on an aid request
  Console/Commands/CreateAdminUser.php

database/migrations/
  ..._create_aid_request_activities_table.php

resources/views/
  welcome.blade.php               # Public landing page
  tracking/show.blade.php         # Public QR shipment tracker
  auth/forgot-password.blade.php, auth/reset-password.blade.php
  dashboards/                     # admin.blade.php, depot-manager.blade.php, coordinator.blade.php
  warehouses/, items/, inventory/, aid-requests/, shipments/, admin/, profile/, reports/, map/
  components/location-picker.blade.php  # Reusable Leaflet map picker
  components/icon.blade.php             # Shared inline SVG icon set
  components/welcome-banner.blade.php   # Gradient dashboard greeting banner
  components/hero-illustration.blade.php  # Landing page SVG illustration
  layouts/app.blade.php           # Sidebar shell with the notification bell for authenticated pages
  layouts/guest.blade.php         # Auth pages shell

lang/ar.json                      # Arabic translations (English is the fallback/default)
tests/Feature/                    # Registration/approval, aid-request lifecycle, admin resource
                                   # management, public pages, AI/logistics services, profile,
                                   # notifications, password reset, audit trail
```

---

## 13. Visual identity

A "field teal" palette distinct from a warm hospitality look — trustworthy and operational rather than decorative: deep teal `#0F6B5C` as the primary action color, amber `#F88A0B` reserved for alerts and low-stock warnings, and cool ink-slate neutrals for text and backgrounds. Typeface: **IBM Plex Sans Arabic** paired with **IBM Plex Sans** for Latin text, giving one consistent look across both languages — self-hosted, so it renders identically regardless of the visitor's network.

The interface is built from a small set of shared components rather than one-off styling: `<x-icon>` (an inline SVG set used throughout the sidebar, stat cards, and section headers), `<x-welcome-banner>` (the gradient greeting at the top of every dashboard), and `<x-hero-illustration>` (the hand-drawn warehouse-to-delivery graphic on the landing page). The landing page also includes a dedicated section showcasing the four AI features, and the Impact Report renders a Chart.js doughnut chart alongside its stat cards.

---

## 14. Privacy note on the public tracking page

`/track/{token}` is reachable by anyone with the QR code — no login. It intentionally shows only what a recipient needs to verify a shipment (status, manifest contents, driver name, origin warehouse name, destination) and **omits** the driver's phone number and precise warehouse GPS coordinates, since this page is public by design and the platform may be used in sensitive contexts.

---

## 15. Before a production deploy

- Production `.env`: `APP_ENV=production`, `APP_DEBUG=false`, a freshly generated `APP_KEY`.
- A real database engine (MySQL/PostgreSQL) instead of SQLite.
- A real mail driver instead of `MAIL_MAILER=log` so account/request/shipment notifications actually reach recipients' inboxes.
- `TWILIO_SID`/`TWILIO_AUTH_TOKEN`/`TWILIO_NUMBER` and/or `ULTRAMSG_INSTANCE_ID`/`ULTRAMSG_TOKEN` if real SMS/WhatsApp alerts to drivers are wanted instead of simulation mode.
- An `OPENAI_API_KEY` if real AI responses are wanted instead of simulation mode.
- `php artisan storage:link` on the server (for delivery photos) and `php artisan config:cache` / `route:cache` for performance.
