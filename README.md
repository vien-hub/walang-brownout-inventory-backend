# Walang Brownout Inventory System (Backend)

Laravel 10 REST API with Sanctum token login. Stores users and the inventory data
(inventory, alerts, transactions, FIFO batches, purchase orders, reports).

## Endpoints
| Method | Path | Notes |
|---|---|---|
| GET | `/api/test-connection` | health check |
| POST | `/api/login`, `/api/register` | returns `token` + `user` |
| GET/POST/PUT/DELETE | `/api/users` | Administrator only for changes |
| GET | `/api/data` | all saved data keys |
| PUT/DELETE | `/api/data/{key}` | keys: inventory_db, alerts_db, transaction_records_db, fifo_batches_db, purchase_orders_db, generated_reports_db |

## Run locally (PHP 8.4, Composer, MySQL)
```bash
composer install
cp .env.example .env && php artisan key:generate
# set DB_* in .env, then:
php artisan migrate --seed
php artisan serve        # http://localhost:8000
php artisan test         # feature tests (SQLite in memory)
```

## Environment variables
`APP_KEY`, `APP_ENV`, `APP_DEBUG`, `APP_URL`, `DB_*` (or `DATABASE_URL`),
`FRONTEND_URL` (allowed CORS origin), `ADMIN_EMAIL`, `ADMIN_PASSWORD`, `ADMIN_NAME`.

## Deploy
See `RAILWAY_DEPLOYMENT_GUIDE.md`.
