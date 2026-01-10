<?php

declare(strict_types=1);

namespace MAAF\Storage\Adapters;

use MAAF\Storage\StorageAdapterInterface;

/**
 * Local Storage Adapter
 * 
 * Lokális fájlrendszer adapter.
 * 
 * @version 1.0.0
 */
final class LocalStorageAdapter implements StorageAdapterInterface
{
    public function __construct(
        private readonly string $rootPath,
        private readonly string $baseUrl = ''
    ) {
        if (!is_dir($this->rootPath)) {
            mkdir($this->rootPath, 0755, true);
        }
    }

    public function exists(string $path): bool
    {
        return file_exists($this->getFullPath($path));
    }

    public function get(string $path): string
    {
        $fullPath = $this->getFullPath($path);
        
        if (!file_exists($fullPath)) {
            throw new \RuntimeException("File not found: {$path}");
        }

        return file_get_contents($fullPath);
    }

    public function put(string $path, string $contents, array $options = []): bool
    {
        $fullPath = $this->getFullPath($path);
        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return file_put_contents($fullPath, $contents) !== false;
    }

    public function delete(string $path): bool
    {
        $fullPath = $this->getFullPath($path);
        
        if (!file_exists($fullPath)) {
            return false;
        }

        return unlink($fullPath);
    }

    public function copy(string $from, string $to): bool
    {
        $fromPath = $this->getFullPath($from);
        $toPath = $this->getFullPath($to);
        $toDir = dirname($toPath);

        if (!is_dir($toDir)) {
            mkdir($toDir, 0755, true);
        }

        return copy($fromPath, $toPath);
    }

    public function move(string $from, string $to): bool
    {
        $fromPath = $this->getFullPath($from);
        $toPath = $this->getFullPath($to);
        $toDir = dirname($toPath);

        if (!is_dir($toDir)) {
            mkdir($toDir, 0755, true);
        }

        return rename($fromPath, $toPath);
    }

    public function size(string $path): int
    {
        $fullPath = $this->getFullPath($path);
        
        if (!file_exists($fullPath)) {
            return 0;
        }

        return filesize($fullPath);
    }

    public function url(string $path): string
    {
        if ($this->baseUrl !== '') {
            return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
        }

        return '/' . ltrim($path, '/');
    }

    public function list(string $path, bool $recursive = false): array
    {
        $fullPath = $this->getFullPath($path);
        
        if (!is_dir($fullPath)) {
            return [];
        }

        $files = [];

        if ($recursive) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $relativePath = str_replace($this->rootPath . '/', '', $file->getPathname());
                    $files[] = $relativePath;
                }
            }
        } else {
            $items = scandir($fullPath);
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..') {
                    $itemPath = $path . '/' . $item;
                    if (is_file($this->getFullPath($itemPath))) {
                        $files[] = $itemPath;
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Get full path
     * 
     * @param string $path Relative path
     * @return string
     */
    private function getFullPath(string $path): string
    {
        $path = str_replace(['../', '..\\'], '', $path);
        return rtrim($this->rootPath, '/') . '/' . ltrim($path, '/');
    }
}
