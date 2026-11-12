<?php

declare(strict_types=1);

/**
 * Front controller. Semua request (selain file statis) masuk ke sini.
 * PRD: bagian 3.1 (System Architecture) dan 4.1.
 *
 * Lokal:  php -S localhost:8000 -t public public/index.php
 * Hosting: document root diarahkan ke folder public/ (lihat PRD 4.7).
 */

// Server bawaan PHP: biarkan file statis (css, js, gambar) dilayani langsung.
if (PHP_SAPI === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $file = __DIR__ . $path;
    if ($path !== '/' && is_file($file)) {
        return false;
    }
}

// Jika folder app/ dipindah ke luar public_html, sesuaikan BASE_PATH ini.
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/bootstrap.php';

$routes = require BASE_PATH . '/app/routes.php';
(new App\Core\Router($routes))->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
