<?php
// Database connection helper: creates SQLite DB and seeds it on first run.

function get_db(array $config): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dbPath = $config['db_path'];
    $isNew = !file_exists($dbPath);

    $dir = dirname($dbPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON;');

    if ($isNew) {
        $initSql = file_get_contents(__DIR__ . '/../db/init.sql');
        $pdo->exec($initSql);

        $seedSql = file_get_contents(__DIR__ . '/../db/seed.sql');
        if ($seedSql !== false && trim($seedSql) !== '') {
            $pdo->exec($seedSql);
        }
    }

    return $pdo;
}
