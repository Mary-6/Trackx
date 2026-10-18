# Aurevia Courier Express — Shipment Tracking Platform

A complete Laravel 12 shipment-tracking application: premium public website, reace-time tracking pages with scannable barcodes, Leaflet/OpenStreetMap live map, role-based admin dashboard, customer management, Sanctum-authenticated location API, and Smartsupp live chat.

## Stack

| Layer | Tech |
|---|---|
| Backend | Laravel 12 / PHP 8.2+ |
| Frontend | Blade + Tailwind CSS 4 + Alpine.js (Vite) |
| Database | MySQL / MariaDB |
| Auth | Laravel Breeze (Blade) — session auth, no public registration |
| API auth | Laravel Sanctum (token abilities) |
| Map | Leaflet 1.9 + OpenStreetMap tiles |
| Barcode | picqer/php-barcode-generator (CODE128, generated server-side) |
| Live chat | Smartsupp official widget (optional, env-configured) |

## Quick start (local / XAMPP)

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Create the database and a user, or reuse root in dev:

```sql
CREATE DATABASE trackx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'trackx'@'localhost' IDENTIFIED BY 'your-password';
GRANT ALL PRIVILEGES ON trackx.* TO 'trackx'@'localhost';
```

Set `DB_*` values in `.env`, then:

```bash
php artisan migrate --seed          # statuses, categories, settings (+ demo data outside production)
php artisan storage:link            # public/ symlink for uploaded images & logo
npm run build                       # or `npm run dev` while developing
php artisan serve                   # http://127.0.0.1:8000
```

### Create the first administrator

There is **no public registration**. Bootstrap the first account:

```bash
php artisan aurevia:make-admin --name="Your Name" --email="you@example.com"
# prompts securely for a password; or pass --password=...
# roles: super_admin (default), manager, support
```

Log in at `/login`. Admin dashboard lives at `/admin` (`/dashboard` redirects there).

## Roles

| Role | Capabilities |
|---|---|
| `super_admin` | Everything: users, settings, API tokens, audit log, all shipment/customer operations |
| `manager` | Create/edit/archive shipments, events, locations, customers, CSV exports |
| `support` | View shipments/customers/enquiries; no editing, no admin pages |

Enforced server-side via `ShipmentPolicy`, `CustomerPolicy`, the `role:` middleware, and `canManageShipments()` on the User model — never just hidden buttons.

## Tracking

- Public lookup at `/track` → `/track` → `/track/T8K3L2M9Q1RX`.
- Results page: **barcode** (CODE128 generated per tracking number), Package Tracking Information header, Shipment Information rows, Receiver/Sender sections, Shipment History table, live map.
- 15 database-driven statuses (`shipment_statuses` table), append-only `tracking_events` history.
- **Privacy-gated parties**: `public_sender_fields` / `public_receiver_fields` settings (Admin → Settings → Public Tracking Page Privacy) decide which sender/receiver fields render. Empty fields never render a row — no "N/A" placeholders.
- Lookups rate-limited per IP (`tracking.throttle`, 30/min); result pages are `noindex`.
- `/track/{number}/location.json` is a privacy-safe feed used by the page's periodic refresh.

## Partial shipments

Only these are mandatory when creating a shipment: **tracking number** (auto-generated if blank, or manual with uniqueness check), **category** (or a new inline one), **status**, and a **title or description**. Everything else — parties, addresses, dates, times, weight, coordinates — is optional and can be filled in later. The public page adapts automatically to whatever exists.

## Map & location data

The map shows the **last confirmed location** plus origin/destination markers and the recorded location path. It never simulates movement.

Location writes:

- **Admin** — `POST /admin/shipments/{id}/location` (labelled `manual`).
- **Integrations** — `POST /api/v1/shipments/{tracking_number}/location` with `Authorization: Bearer <token>` and `{"latitude": ..., "longitude": ..., "location_name": ..., "recorded_at": ...}`. Tokens are issued at **Admin → API Tokens** with the `update-location` ability; coordinates/timestamps are server-validated; endpoint is rate-limited and requires Sanctum auth. Use HTTPS in production.

## Smartsupp

1. Create an account at smartsupp.com → **Settings → Chat box → Chat code** → copy `Your key`.
2. Put it in `.env`: `SMARTSUPP_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`.
3. The official `loader.js` widget then appears on all public pages (once). Empty key = no widget at all.
4. Operating hours, colours, and agent management live in the Smartsupp dashboard. Nothing sensitive is sent — the widget carries no shipment context.

## Email notifications

- `App\Notifications\ShipmentStatusNotification` (queued, `ShouldQueue`) is dispatched when admins check *notify recipient* on shipment creation or event updates.
- Every attempt is recorded in `notification_records` (sent/failed/skipped) and de-duplicated per shipment+type+recipient; failures are logged and visible on the shipment's admin page — nothing claims to send when it didn't.
- Toggle globally under **Admin → Settings → Email notifications**. Configure `MAIL_*` in `.env`; `MAIL_MAILER=log` writes mail to the log for development.

Run the queue worker for queued delivery:

```bash
php artisan queue:work        # QUEUE_CONNECTION=database (jobs table)
```

## Production deployment

- **Web root** must point at `public/` (Apache vhost `DocumentRoot .../public`, or Nginx `root` + `try_files`). Do not serve from the project root; `.env` and `storage/` must never be web-accessible.
- A ready `.env.production` template ships in the repo — copy it to `.env` on the server, run `php artisan key:generate`, and fill in DB + mail credentials.
- `php artisan config:cache route:cache view:cache` after deploy; `php artisan optimize:clear` to reset.
- Queue worker via Supervisor: `php artisan queue:work --sleep=3 --tries=3`.
- Scheduler via cron (Linux): `* * * * * php /path/to/artisan schedule:run` — purges expired API tokens daily.
- Enforce HTTPS at the web server (redirect 80→443; a free Let's Encrypt cert is fine). The app also forces `https` for generated URLs when `APP_ENV=production`.
- Backups: `php artisan aurevia:backup` dumps the DB + uploaded files into `storage/app/backups/` (keeps 14 days). It runs nightly via the scheduler (`schedule:run` cron). Set `MYSQLDUMP_PATH` in `.env`. Also copy the backups off-server and test restores.
- Health check: `GET /up` returns 200 when the app is live.

### Seeders in production

`db:seed` runs statuses/categories/settings only; demo shipments/customers seed **only** outside `APP_ENV=production`.

## Testing

```bash
php artisan test        # 82 tests — PHPUnit, in-memory SQLite
```

Covers: public pages, tracking lookups (valid/invalid/archived), barcode rendering, empty-field omission, receiver/sender privacy gating, history ordering, privacy leaks, RBAC for all three roles, minimace-field shipment creation, manual tracking numbers, append-only events, manual + API location writes (auth, ability, coordinate/timestamp validation), CSV exports, login/logout/no-registration, contact form, notification success/failure/dedup/disable, Smartsupp presence/absence.

## Structure highlights

```
app/Services/        ShipmentService, LocationService, TrackingNumberGenerator,
                     AuditLogger, NotificationDispatcher
app/Policies/        ShipmentPolicy, CustomerPolicy
app/Http/Middleware/ EnsureUserHasRole (role:), ThrottleTrackingLookups
app/Models/          Shipment, TrackingEvent, ShipmentLocation, Customer,
                     ShipmentCategory, ShipmentStatus, SiteSetting,
                     NotificationRecord, AuditLog, ContactMessage
app/Console/Commands aurevia:make-admin
routes/api.php       Sanctum-protected location endpoint
public/images/       Logistics photography used by the public pages
```

## Honour-system notes

- The map displays **real recorded data only** — there is no fake GPS animation.
- Manual admin location updates are labelled `manual`; integration writes are `integration`.
- Archived shipments vanish from public tracking but keep their full history in admin.
