<?php

namespace App\Core;

class View {
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): void {
        extract($data);

        // Check if admin view or customer view
        if (str_starts_with($template, 'admin/')) {
            $viewPath = dirname(__DIR__) . '/Admin/Views/' . substr($template, 6) . '.php';
            $layoutPath = $layout ? dirname(__DIR__) . '/Admin/Views/' . $layout . '.php' : null;
        } else {
            $viewPath = dirname(__DIR__) . '/Views/' . $template . '.php';
            $layoutPath = $layout ? dirname(__DIR__) . '/Views/' . $layout . '.php' : null;
        }

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "View file not found: {$template}";
            return;
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layoutPath && file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }
}
