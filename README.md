# GBOffers — Group up. Pay less.

GBOffers is a group-buying marketplace where people combine their purchasing power to unlock better prices from merchants. Buyers join **groups**, merchants confirm mobile-money payments and redeem QR **GBPasses**, and the platform takes commission.

Built with Laravel 13, Livewire 4, Flux UI and Tailwind CSS 4. Currency UGX, timezone Africa/Kampala.

## Features

**Buyers**

- Explore offers with live search, category pills and grid / list / showcase views
- Join groups, invite friends via WhatsApp, track fills with live progress bars
- Checkout with MoMo, Flutterwave, iOTEC or direct transfer (switchable per order)
- GBPass wallet with QR + PIN redemption, order ledger, wanted board (guests welcome)
- Notifications bell, dark/light mode, PWA support

**Merchants** (`/merchant`)

- Dashboard with revenue charts, payments inbox, QR scanner, orders, commissions
- Post offers manually, import from a product URL, or set group-specific discounts per buyer segment
- Paid featuring (UGX 1,000/day · 10 days for 6,500), public shop pages at `/@handle`, analytics
- New shops go through an approval queue

**Admins** (`/admin`)

- Full oversight: offers, groups, orders, users (roles, suspend, verify, login-as), merchants, disputes
- Analytics, view + search-trend insights, featuring queue, URL importer
- Database notifications across all key events

## Requirements

- PHP 8.3+, Composer, Node 20+, SQLite (default) or MySQL

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed   # seeds demo merchants, 100 offers, gangs, users
npm install && npm run build
php artisan serve            # app at http://127.0.0.1:8000
```

Queue mail is synchronous by default, so no worker is needed locally. For production set
`QUEUE_CONNECTION=database` and run `php artisan queue:work`.

## Demo accounts (password: `password`)

| Role     | Email                | Notes                                  |
| -------- | -------------------- | -------------------------------------- |
| Merchant | `merchant@demo.ug`   | Owns 8 demo shops                      |
| Admin    | `admin@demo.ug`      | Create via seeder or promote a user    |
| Customer | `customer1@demo.ug`…`customer6@demo.ug` | Pre-verified buyers           |

## Mail

Verification and notification mail goes through the configured SMTP mailer
(`MAIL_MAILER`, `MAIL_HOST`, … in `.env`). With the `log` driver, messages land in
`storage/logs/laravel.log` — handy for local development.

## Key routes

| Page | URL |
| ---- | --- |
| Storefront / explore / offer | `/`, `/explore`, `/offers/{slug}` |
| Groups / orders / wallet | `/groups`, `/orders`, `/wallet` |
| Merchant shop (public) | `/@{slug}` e.g. `/@city-style` |
| Merchant area | `/merchant/…` |
| Admin console | `/admin/…` |

## Checks

```bash
php artisan test          # full suite (100+ tests)
vendor/bin/pint --test    # style
vendor/bin/phpstan analyse # static analysis
npm run build             # production assets
```

## License

MIT. Brand icons by [Lucide](https://lucide.dev) (ISC).
