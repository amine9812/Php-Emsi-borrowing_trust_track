## Built by

This project was built by :

- **Zine Mohamed Amine** 
- **Ghaout Khalid** 
- **Itmine mehdi** 

> Module/Context: **PhP PROJECT** 
# Borrower Trust Score (BTS)

A small PHP 8 + SQLite app to track borrowed items and compute a transparent trust score per borrower.

## Run the app

```bash
php -S localhost:8000 -t public
```

Then open: `http://localhost:8000/?page=dashboard`

## Database initialization

On first run, if `db/app.sqlite` does not exist, the app will automatically:

1. Create the SQLite database file
2. Run `db/init.sql`
3. Run `db/seed.sql`

To reset the data, delete `db/app.sqlite` and refresh.

## Routes

- Dashboard: `/?page=dashboard`
- Borrowers list: `/?page=borrowers`
- Borrower details: `/?page=borrower&id=1`
- Items: `/?page=items`
- Loans: `/?page=loans`

## Trust score rules (0-100)

- Start at 100, clamp to [0, 100]
- On-time return (days late = 0): +1
- Late return: -2 per day late
- Damage penalties: minor -15, major -30
- Lost item: -50

All score changes are logged in the Trust Events table and visible on the borrower details page.
