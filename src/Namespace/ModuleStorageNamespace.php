<?php

declare(strict_types=1);

namespace MAAF\Storage\Namespace;

use MAAF\Storage\StorageManager;

/**
 * Module Storage Namespace
 * 
 * Modulonkénti storage namespace kezelés.
 * 
 * @version 1.0.0
 */
final class ModuleStorageNamespace
{
    /**
     * @var array<string, string>
     */
    private array $namespaces = [];

    public function __construct(
        private readonly StorageManager $storageManager
    ) {
    }

    /**
     * Register namespace for module
     * 
     * @param string $moduleName Module name
     * @param string $namespace Namespace path
     * @return void
     */
    public function register(string $moduleName, string $namespace): void
    {
        $this->namespaces[$moduleName] = rtrim($namespace, '/');
    }

    /**
     * Get namespace path for module
     * 
     * @param string $moduleName Module name
     * @return string
     */
    public function getNamespace(string $moduleName): string
    {
        return $this->namespaces[$moduleName] ?? "modules/{$moduleName}";
    }

    /**
     * Get full path for module file
     * 
     * @param string $moduleName Module name
     * @param string $path File path
     * @return string
     */
    public function getPath(string $moduleName, string $path): string
    {
        $namespace = $this->getNamespace($moduleName);
        return $namespace . '/' . ltrim($path, '/');
    }

    /**
     * Check if file exists in module namespace
     * 
     * @param string $moduleName Module name
     * @param string $path File path
     * @param string|null $adapter Adapter name
     * @return bool
     */
    public function exists(string $moduleName, string $path, ?string $adapter = null): bool
    {
        $fullPath = $this->getPath($moduleName, $path);
        return $this->storageManager->exists($fullPath, $adapter);
    }

    /**
     * Get file from module namespace
     * 
     * @param string $moduleName Module name
     * @param string $path File path
     * @param string|null $adapter Adapter name
     * @return string
     */
    public function get(string $moduleName, string $path, ?string $adapter = null): string
    {
        $fullPath = $this->getPath($moduleName, $path);
        return $this->storageManager->get($fullPath, $adapter);
    }

    /**
     * Put file to module namespace
     * 
     * @param string $moduleName Module name
     * @param string $path File path
     * @param string $contents File contents
     * @param array<string, mixed> $options Options
     * @param string|null $adapter Adapter name
     * @return bool
     */
    public function put(string $moduleName, string $path, string $contents, array $options = [], ?string $adapter = null): bool
    {
        $fullPath = $this->getPath($moduleName, $path);
        return $this->storageManager->put($fullPath, $contents, $options, $adapter);
    }

    /**
     * Get file URL from module namespace
     * 
     * @param string $moduleName Module name
     * @param string $path File path
     * @param string|null $adapter Adapter name
     * @return string
     */
    public function url(string $moduleName, string $path, ?string $adapter = null): string
    {
        $fullPath = $this->getPath($moduleName, $path);
        return $this->storageManager->url($fullPath, $adapter);
    }
}
