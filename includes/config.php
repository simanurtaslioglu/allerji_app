<?php
// includes/config.php
// Railway ortam değişkenlerini otomatik algılar, XAMPP'ta da çalışır

$db_url = getenv('DATABASE_URL');

if ($db_url) {
    $parsed = parse_url($db_url);
    define('DB_HOST', $parsed['host']);
    define('DB_PORT', $parsed['port'] ?? 3306);
    define('DB_USER', $parsed['user']);
    define('DB_PASS', $parsed['pass']);
    define('DB_NAME', ltrim($parsed['path'], '/'));
} else {
    define('DB_HOST', getenv('MYSQLHOST')     ?: 'localhost');
    define('DB_PORT', getenv('MYSQLPORT')     ?: 3306);
    define('DB_USER', getenv('MYSQLUSER')     ?: 'root');
    define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
    define('DB_NAME', getenv('MYSQLDATABASE') ?: 'allerji_db');
}

$domain = getenv('RAILWAY_PUBLIC_DOMAIN');
define('BASE_URL', $domain ? 'https://' . $domain : 'http://localhost/allerji_app');
define('APP_NAME', 'AlerjiBekçisi');

session_start();

function db() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Veritabanı bağlantısı kurulamadı.']));
        }
    }
    return $pdo;
}

function isLoggedIn()  { return isset($_SESSION['kullanici_id']); }

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/index.php?page=giris');
        exit;
    }
}

function currentUser() {
    if (!isLoggedIn()) return null;
    $stmt = db()->prepare("SELECT * FROM kullanicilar WHERE id = ?");
    $stmt->execute([$_SESSION['kullanici_id']]);
    return $stmt->fetch();
}

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
