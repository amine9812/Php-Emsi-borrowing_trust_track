<?php
// Front controller: bootstraps the app, routes requests, and loads the layout.

session_start();

$config = require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/db.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/repo/BorrowerRepo.php';
require __DIR__ . '/../app/repo/ItemRepo.php';
require __DIR__ . '/../app/repo/LoanRepo.php';
require __DIR__ . '/../app/services/TrustService.php';

$db = get_db($config);

$routes = [
    'dashboard' => ['file' => __DIR__ . '/../pages/dashboard.php', 'title' => 'Dashboard'],
    'borrowers' => ['file' => __DIR__ . '/../pages/borrowers.php', 'title' => 'Borrowers'],
    'borrower' => ['file' => __DIR__ . '/../pages/borrower_show.php', 'title' => 'Borrower Details'],
    'items' => ['file' => __DIR__ . '/../pages/items.php', 'title' => 'Items'],
    'loans' => ['file' => __DIR__ . '/../pages/loans.php', 'title' => 'Loans'],
];

$pageKey = $_GET['page'] ?? 'dashboard';
$route = $routes[$pageKey] ?? null;
$pageFile = $route['file'] ?? null;
$pageTitle = $route['title'] ?? 'Not Found';

include __DIR__ . '/../views/layout.php';
