# Borrower Trust Score (BTS) - Laravel Edition

A Laravel 12 + SQLite app to track borrowed items and compute transparent trust scores per borrower.

## Requirements

- PHP 8.3+
- SQLite extension for PHP (`pdo_sqlite`)
- Composer

> Note: Laravel dev tools require the PHP XML extensions (`dom`, `xml`, `xmlwriter`). If Composer errors on those, install `php-xml` or use the ignore flags shown below.

## Setup

```bash
# from the project root
php ./composer install --no-interaction --ignore-platform-req=ext-xml --ignore-platform-req=ext-dom --ignore-platform-req=ext-xmlwriter

# create the SQLite database file (already present, but safe to run)
touch database/app.sqlite

# run migrations + seed data
php artisan migrate --seed
```

## Run the app

```bash
php artisan serve
```

Open: `http://localhost:8000`

## Routes

- Dashboard: `/`
- Borrowers list: `/borrowers`
- Borrower details: `/borrowers/{id}`
- Items: `/items`
- Loans: `/loans`

## Trust score rules (0-100)

- Start at 100, clamp to [0, 100]
- On-time return (days late = 0): +1
- Late return: -2 per day late
- Damage penalties: minor -15, major -30
- Lost item: -50

All score changes are logged in the Trust Events table and visible on the borrower details page.

## Notes

- The original plain-PHP version is preserved in `legacy/`.
- Laravel uses Blade templates which escape output by default to prevent XSS.
