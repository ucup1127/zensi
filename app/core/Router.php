<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    /** @var array<string, array<int, array{pattern:string, handler:mixed, middleware:array}>> */
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'DELETE' => [],
        'PATCH'  => [],
    ];

    private array $groupStack = [];

    public function get(string $path, mixed $handler, array $middleware = []): self
    {
        return $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, mixed $handler, array $middleware = []): self
    {
        return $this->add('POST', $path, $handler, $middleware);
    }

    public function put(string $path, mixed $handler, array $middleware = []): self
    {
        return $this->add('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, mixed $handler, array $middleware = []): self
    {
        return $this->add('DELETE', $path, $handler, $middleware);
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    private function add(string $method, string $path, mixed $handler, array $middleware): self
    {
        $prefix = '';
        $groupMiddleware = [];
        foreach ($this->groupStack as $g) {
            $prefix .= rtrim($g['prefix'] ?? '', '/');
            $groupMiddleware = array_merge($groupMiddleware, $g['middleware'] ?? []);
        }

        $fullPath = '/' . trim($prefix . '/' . trim($path, '/'), '/');
        $fullPath = $fullPath === '/' ? '/' : rtrim($fullPath, '/');

        // Convert {param} to named regex
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $fullPath);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[$method][] = [
            'pattern'    => $pattern,
            'handler'    => $handler,
            'middleware' => array_merge($groupMiddleware, $middleware),
        ];
        return $this;
    }

    public function dispatch(): void
    {
        $method = Request::method();
        $path   = Request::path();

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                $params = array_filter(
                    $matches,
                    fn($k) => !is_int($k),
                    ARRAY_FILTER_USE_KEY
                );

                // === TAMBAHKAN KODE INI ===
                // Otomatis konversi parameter yang berupa angka menjadi integer
                $params = array_map(function($val) {
                    return is_numeric($val) ? (int) $val : $val;
                }, $params);
                // ===========================

                // Middleware pipeline
                foreach ($route['middleware'] as $mw) {
                    $result = is_callable($mw) ? $mw() : (new $mw())->handle();
                    if ($result === false) {
                        return; // middleware sudah redirect/exit
                    }
                }

                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        echo '<h1>404</h1><p>Halaman tidak ditemukan.</p>';
    }

    private function callHandler(mixed $handler, array $params): void
    {
        if (is_callable($handler)) {
            $handler(...array_values($params));
            return;
        }
        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);
            $instance = new $class();
            $instance->$method(...array_values($params));
            return;
        }
        throw new \RuntimeException('Handler tidak valid.');
    }
}