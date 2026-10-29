<?php

namespace App\Services;

use RuntimeException;

class ImageUploadService {
    public static function upload(array $file, string $subfolder = 'products'): string {
        if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Image upload error code: " . ($file['error'] ?? 'unknown'));
        }

        $maxSize = config('security.max_upload_size', 5 * 1024 * 1024);
        if ($file['size'] > $maxSize) {
            throw new RuntimeException("Uploaded image exceeds the 5MB size limit.");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg'
        ];

        if (!array_key_exists($mime, $allowedMimes)) {
            throw new RuntimeException("Invalid image format. Allowed formats: JPG, PNG, WEBP, SVG.");
        }

        $ext = $allowedMimes[$mime];
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;

        $targetDir = dirname(__DIR__, 2) . '/public/uploads/' . trim($subfolder, '/');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException("Failed to save uploaded image.");
        }

        return '/uploads/' . trim($subfolder, '/') . '/' . $filename;
    }
}
