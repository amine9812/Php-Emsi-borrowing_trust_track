-- Seed data for quick demo.

INSERT INTO borrowers (name, email, phone, trust_score, created_at) VALUES
('Alice Nguyen', 'alice@example.com', '555-2100', 100, '2024-06-01T09:00:00+00:00'),
('Marcus Cole', 'marcus@example.com', '555-3344', 81, '2024-06-02T11:15:00+00:00'),
('Priya Shah', 'priya@example.com', '555-7788', 100, '2024-06-03T15:40:00+00:00');

INSERT INTO items (name, category, serial, notes, is_active, created_at) VALUES
('DSLR Camera', 'Photography', 'CAM-8842', 'Includes lens kit', 1, '2024-06-01T09:10:00+00:00'),
('Laptop Dell 14"', 'Electronics', 'DL-1422', 'Charger included', 1, '2024-06-01T09:12:00+00:00'),
('Mini Projector', 'Presentation', 'PJ-330', 'Needs HDMI cable', 1, '2024-06-01T09:14:00+00:00');

INSERT INTO loans (borrower_id, item_id, loan_date, due_date, returned_at, status, return_condition, notes) VALUES
(1, 1, '2024-07-01', '2024-07-10', NULL, 'open', NULL, 'Student film project'),
(2, 2, '2024-05-20', '2024-05-27', '2024-05-29', 'returned', 'damaged_minor', 'Returned with minor scratches');

INSERT INTO trust_events (borrower_id, loan_id, event_type, points_delta, reason, created_at) VALUES
(2, 2, 'late_penalty', -4, 'Late return: 2 day(s)', '2024-05-29T10:00:00+00:00'),
(2, 2, 'damage_penalty', -15, 'Damage penalty: minor', '2024-05-29T10:00:00+00:00');
