<?php

declare(strict_types=1);

namespace App\Core;

use App\Auth\AdminAuth;
use App\Auth\ParticipantAuth;
use RuntimeException;

/**
 * Router sederhana untuk daftar rute di app/routes.php.
 * PRD: 4.3, 4.4
 *
 *   - __construct(array $routes)
 *   - dispatch(string $method, string $uri): void — cocokkan path, dukung {id} angka
 *   - Verifikasi CSRF untuk semua POST sebelum controller (gagal → 419)
 *   - Jalankan middleware: public, guest, participant, admin
 *   - Instansiasi controller dan panggil method dengan parameter {id} sebagai int
 *   - Tidak cocok → render errors/404 dengan status 404
 */
final class Router
{
    /** @var list<array{method: string, regex: string, handler: array, middleware: string}> */
    private array $routes = [];

    public function __construct(array $routes)
    {
        foreach ($routes as [$method, $path, $handler, $middleware]) {
            $this->routes[] = [
                'method'     => strtoupper($method),
                'regex'      => $this->compile($path),
                'handler'    => $handler,
                'middleware' => $middleware,
            ];
        }
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method) === 'HEAD' ? 'GET' : strtoupper($method);
        $path = $this->normalizePath($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method || !preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            if ($method === 'POST' && !Csrf::verify($_POST['_csrf'] ?? null)) {
                View::error(419, ['title' => 'Sesi form kedaluwarsa']);
                return;
            }

            $this->runMiddleware($route['middleware']);

            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[] = (int) $value;
                }
            }

            [$class, $action] = $route['handler'];
            if (!class_exists($class) || !method_exists($class, $action)) {
                throw new RuntimeException("Handler belum dibuat: {$class}::{$action}");
            }

            (new $class())->{$action}(...$params);
            return;
        }

        View::error(404, ['title' => 'Halaman tidak ditemukan']);
    }

    /** '/admin/jadwal/hari/{id}/edit' → '~^/admin/jadwal/hari/(?P<id>\d+)/edit$~' */
    private function compile(string $path): string
    {
        $parts = preg_split('~(\{[a-z_]+\})~i', $path, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
        $regex = '';
        foreach ($parts as $part) {
            if (preg_match('~^\{([a-z_]+)\}$~i', $part, $m)) {
                $regex .= '(?P<' . $m[1] . '>\d{1,10})';
            } else {
                $regex .= preg_quote($part, '~');
            }
        }
        return '~^' . $regex . '$~';
    }

    /** Ambil path dari URI, buang query string dan path dasar (APP_URL), rapikan garis miring akhir. */
    private function normalizePath(string $uri): string
    {
        $path = rawurldecode(parse_url($uri, PHP_URL_PATH) ?: '/');

        $base = base_path();
        if ($base !== '' && ($path === $base || str_starts_with($path, $base . '/'))) {
            $path = substr($path, strlen($base));
        }

        $path = '/' . trim($path, '/');
        return $path;
    }

    private function runMiddleware(string $middleware): void
    {
        switch ($middleware) {
            case 'public':
                return;

            case 'guest':
                // Peserta yang sudah masuk tidak perlu melihat halaman awal, daftar, atau masuk (PRD 2.2.2).
                if (ParticipantAuth::id() !== null) {
                    redirect('/info');
                }
                return;

            case 'participant':
                ParticipantAuth::requireParticipant();
                return;

            case 'admin':
                if (!AdminAuth::check()) {
                    redirect('/admin/login');
                }
                return;

            default:
                throw new RuntimeException('Middleware tidak dikenal: ' . $middleware);
        }
    }
}
