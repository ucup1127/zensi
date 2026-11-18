<?php
declare(strict_types=1);

namespace App\Core;

class View
{
    private static array $sections = [];

    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        $viewPath = BASE_PATH . '/app/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View tidak ditemukan: {$view}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout) {
            $layoutPath = BASE_PATH . '/app/views/layouts/' . $layout . '.php';
            if (!file_exists($layoutPath)) {
                throw new \RuntimeException("Layout tidak ditemukan: {$layout}");
            }
            require $layoutPath;
            return;
        }

        echo $content;
    }

    public static function section(string $name): string
    {
        return self::$sections[$name] ?? '';
    }

    public static function start(string $name): void
    {
        ob_start();
        self::$sections[$name . '_start'] = $name;
    }

    public static function stop(string $name): void
    {
        if (isset(self::$sections[$name . '_start'])) {
            self::$sections[$name] = ob_get_clean();
            unset(self::$sections[$name . '_start']);
        }
    }
}

// Helper untuk view
if (!function_exists('yield_section')) {
    function yield_section(string $name): string {
        return \App\Core\View::section($name);
    }
}
if (!function_exists('start_section')) {
    function start_section(string $name): void {
        \App\Core\View::start($name);
    }
}
if (!function_exists('end_section')) {
    function end_section(): void {
        // Dapatkan nama section yang sedang aktif
        $sections = new \ReflectionClass(\App\Core\View::class);
        $prop = $sections->getProperty('sections');
        $prop->setAccessible(true);
        $vals = $prop->getValue();
        foreach ($vals as $k => $v) {
            if (str_ends_with($k, '_start')) {
                \App\Core\View::stop($v);
                break;
            }
        }
    }
}