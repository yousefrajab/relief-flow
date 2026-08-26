# ReliefFlow

A humanitarian logistics coordination platform connecting **relief warehouses**, **depot managers**, and **field coordinators** to track relief item stock, route aid requests, and confirm deliveries with a QR-verified dispatch manifest. Built with **Laravel 12**, fully **bilingual (Arabic/English)** with automatic RTL/LTR switching.

---

## 1. How it works

Every field request moves through a clear state machine on the `aid_requests` table:

```
pending  →  dispatched  →  delivered
    ↓
 rejected (a depot manager or admin can decline a request that can't be fulfilled)
```

1. A **field coordinator** submits an aid request: a target distribution location, notes, and one or more relief items with quantities.
2. A **depot manager** (or admin) reviews it and either **rejects** it with a reason, or **dispatches** it — picking a source warehouse and driver details. Dispatch checks stock for every requested item across the chosen warehouse; if anything is short, the whole dispatch is rejected with the exact shortfall shown, and nothing is deducted.
3. On a successful dispatch, stock is deducted from that warehouse, a **shipment** record is created with a unique QR tracking token, and a printable manifest becomes available.
4. The coordinator confirms receipt in the field once the goods arrive, closing out both the shipment and the original aid request as **delivered**.

---

## 2. Roles

| Role | Value | How the account is created | Starting status |
|---|---|---|---|
| Administrator | `admin` | **Only** via `php artisan app:create-admin` — no public signup | `active` |
| Depot Manager | `depot_manager` | Public registration | `pending_verification` (needs admin approval) |
| Field Coordinator | `coordinator` | Public registration | `pending_verification` (needs admin approval) |

Account status: `pending_verification` / `active` / `suspended`. A pending or suspended user is redirected to an explanatory waiting page instead of the dashboard.

**Admin:** manages warehouses and relief items, approves/suspends accounts, and can also reject/dispatch requests and confirm deliveries — full visibility across the platform.

**Depot Manager:** manages warehouse stock levels, reviews pending aid requests, rejects or dispatches them, and tracks shipments already on the road.

**Field Coordinator:** submits aid requests with any number of relief items, tracks their own requests, and confirms delivery once a shipment reaches them.

---

## 3. Bilingual support

Every page renders in Arabic or English based on the session locale, switchable from the sidebar/login screen at any time. The `<html dir>` attribute flips automatically between `rtl` and `ltr`, and the layout uses CSS logical properties (`ms-`, `me-`, `ps-`, `pe-`, `text-start`/`text-end`) so spacing and alignment mirror correctly in both directions without duplicated markup. Translations live in `lang/ar.json` (English strings are the translation keys, so English needs no separate file).

---

## 4. Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Auth | Session-based, hand-rolled registration + approval flow (no third-party auth package) |
| Database | SQLite for local development (swap via `.env` for MySQL/PostgreSQL in production) |
| Frontend | Blade + Alpine.js + Tailwind CSS v4 |
| Build | Vite |
| QR codes | `bacon/bacon-qr-code` — generated locally as inline SVG, no external API call |
| Tests | PHPUnit (Feature tests) |

---

## 5. Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate

npm install
npm run build   # or: npm run dev

php artisan serve
```

### Create the administrator account

There is no public signup for admins — create the first (and any further) admin from the terminal:

```bash
php artisan app:create-admin
```

### Demo data

`php artisan db:seed` creates a depot manager and a field coordinator (`manager@reliefflow.com` / `coordinator@reliefflow.com`, password `password`), three sample warehouses, four relief items, and some starting stock — useful for trying the full flow immediately after `app:create-admin`.

---

## 6. Tests

```bash
php artisan test
```

Feature tests cover: registration and admin approval (including that pending/suspended accounts can't reach the dashboard and nobody can self-register as admin), the full aid-request lifecycle (multi-item requests, dispatch with stock deduction, insufficient-stock rejection, request rejection, delivery confirmation and who's allowed to confirm it), and admin-only warehouse/item/inventory management (including that a warehouse with shipment history or an item with stock can't be deleted).

---

## 7. Project structure

```
app/
  Http/Controllers/
    AuthController.php            # Login / logout
    Auth/RegisteredUserController.php
    AdminController.php           # Approve / suspend accounts
    DashboardController.php       # Per-role dashboard data
    WarehouseController.php       # Admin-only CRUD
    ItemController.php            # Admin-only CRUD
    InventoryController.php       # Add stock (admin + depot manager)
    AidRequestController.php      # Submit / reject / dispatch
    ShipmentController.php        # Confirm delivery / print manifest
  Policies/
    AidRequestPolicy.php          # create / reject / dispatch / confirmDelivery
    ShipmentPolicy.php            # view / deliver
  Http/Middleware/
    EnsureUserIsAdmin.php
    EnsureUserIsActive.php        # Gates the dashboard for pending/suspended accounts
    SetLocale.php
  Services/QrCodeService.php      # Local SVG QR generation
  Console/Commands/CreateAdminUser.php

resources/views/
  dashboards/                     # admin.blade.php, depot-manager.blade.php, coordinator.blade.php
  partials/aid-request-list.blade.php
  shipments/print.blade.php       # Printable dispatch manifest with embedded QR
  layouts/app.blade.php           # Sidebar shell for authenticated pages
  layouts/guest.blade.php         # Auth pages shell

lang/ar.json                      # Arabic translations (English is the fallback/default)
tests/Feature/                    # Registration/approval, aid-request lifecycle, admin resource management
```

---

## 8. Visual identity

A "field teal" palette distinct from a warm hospitality look — trustworthy and operational rather than decorative: deep teal `#0F6B5C` as the primary action color, amber `#F88A0B` reserved for alerts and low-stock warnings, and cool ink-slate neutrals for text and backgrounds. Typeface: **IBM Plex Sans Arabic** paired with **IBM Plex Sans** for Latin text, giving one consistent look across both languages.

---

## 9. Before a production deploy

- Production `.env`: `APP_ENV=production`, `APP_DEBUG=false`, a freshly generated `APP_KEY`.
- A real database engine (MySQL/PostgreSQL) instead of SQLite.
- A real mail driver if email notifications are added later (none are wired up yet — approvals and dispatches currently only show as in-app flash messages).
- `php artisan config:cache` and `php artisan route:cache` for performance.
