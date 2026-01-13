-- Schema for Borrower Trust Score (BTS).

CREATE TABLE borrowers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT,
    phone TEXT,
    trust_score INTEGER NOT NULL DEFAULT 100,
    created_at TEXT NOT NULL
);

CREATE TABLE items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    category TEXT,
    serial TEXT,
    notes TEXT,
    is_active INTEGER NOT NULL DEFAULT 1,
    created_at TEXT NOT NULL
);

CREATE TABLE loans (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    borrower_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    loan_date TEXT NOT NULL,
    due_date TEXT NOT NULL,
    returned_at TEXT,
    status TEXT NOT NULL DEFAULT 'open',
    return_condition TEXT,
    notes TEXT,
    FOREIGN KEY(borrower_id) REFERENCES borrowers(id),
    FOREIGN KEY(item_id) REFERENCES items(id)
);

CREATE TABLE trust_events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    borrower_id INTEGER NOT NULL,
    loan_id INTEGER,
    event_type TEXT NOT NULL,
    points_delta INTEGER NOT NULL,
    reason TEXT NOT NULL,
    created_at TEXT NOT NULL,
    FOREIGN KEY(borrower_id) REFERENCES borrowers(id),
    FOREIGN KEY(loan_id) REFERENCES loans(id)
);
