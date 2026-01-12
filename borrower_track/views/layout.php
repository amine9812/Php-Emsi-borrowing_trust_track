<?php
// Main layout: shared HTML shell, navigation, and page inclusion.

$flashMessages = flash_get_all();
$activePage = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($pageTitle); ?> | <?php echo e($config['app_name']); ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <!-- Site header and navigation -->
    <header class="site-header">
        <div class="container">
            <div class="brand">
                <div class="brand-mark">BTS</div>
                <div>
                    <div class="brand-title"><?php echo e($config['app_name']); ?></div>
                    <div class="brand-sub">Borrower trust, simplified</div>
                </div>
            </div>
            <nav class="nav">
                <a href="/?page=dashboard" class="<?php echo $activePage === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
                <a href="/?page=borrowers" class="<?php echo $activePage === 'borrowers' ? 'active' : ''; ?>">Borrowers</a>
                <a href="/?page=items" class="<?php echo $activePage === 'items' ? 'active' : ''; ?>">Items</a>
                <a href="/?page=loans" class="<?php echo $activePage === 'loans' ? 'active' : ''; ?>">Loans</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <!-- Flash messages -->
        <?php if (!empty($flashMessages)) : ?>
            <div class="flash-stack">
                <?php foreach ($flashMessages as $flash) : ?>
                    <div class="flash flash-<?php echo e($flash['type']); ?>">
                        <?php echo e($flash['message']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Page content -->
        <?php if ($pageFile && file_exists($pageFile)) : ?>
            <?php include $pageFile; ?>
        <?php else : ?>
            <section class="card">
                <h1>Page not found</h1>
                <p>The page you requested does not exist.</p>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
