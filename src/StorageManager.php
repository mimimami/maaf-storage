<?php

declare(strict_types=1);

namespace MAAF\Storage;

/**
 * Storage Manager
 * 
 * Storage kezelő több adapter támogatással.
 * 
 * @version 1.0.0
 */
final class StorageManager
{
    /**
     * @var array<string, StorageAdapterInterface>
     */
    private array $adapters = [];

    private string $defaultAdapter = 'local';

    /**
     * Register adapter
     * 
     * @param string $name Adapter name
     * @param StorageAdapterInterface $adapter Adapter instance
     * @return void
     */
    public function registerAdapter(string $name, StorageAdapterInterface $adapter): void
    {
        $this->adapters[$name] = $adapter;
    }

    /**
     * Set default adapter
     * 
     * @param string $name Adapter name
     * @return void
     */
    public function setDefaultAdapter(string $name): void
    {
        if (!isset($this->adapters[$name])) {
            throw new \RuntimeException("Adapter not found: {$name}");
        }

        $this->defaultAdapter = $name;
    }

    /**
     * Get adapter
     * 
     * @param string|null $name Adapter name (null = default)
     * @return StorageAdapterInterface
     */
    public function getAdapter(?string $name = null): StorageAdapterInterface
    {
        $name = $name ?? $this->defaultAdapter;

        if (!isset($this->adapters[$name])) {
            throw new \RuntimeException("Adapter not found: {$name}");
        }

        return $this->adapters[$name];
    }

    /**
     * Check if file exists
     * 
     * @param string $path File path
     * @param string|null $adapter Adapter name (null = default)
     * @return bool
     */
    public function exists(string $path, ?string $adapter = null): bool
    {
        return $this->getAdapter($adapter)->exists($path);
    }

    /**
     * Get file contents
     * 
     * @param string $path File path
     * @param string|null $adapter Adapter name (null = default)
     * @return string
     */
    public function get(string $path, ?string $adapter = null): string
    {
        return $this->getAdapter($adapter)->get($path);
    }

    /**
     * Put file contents
     * 
     * @param string $path File path
     * @param string $contents File contents
     * @param array<string, mixed> $options Options
     * @param string|null $adapter Adapter name (null = default)
     * @return bool
     */
    public function put(string $path, string $contents, array $options = [], ?string $adapter = null): bool
    {
        return $this->getAdapter($adapter)->put($path, $contents, $options);
    }

    /**
     * Get file URL
     * 
     * @param string $path File path
     * @param string|null $adapter Adapter name (null = default)
     * @return string
     */
    public function url(string $path, ?string $adapter = null): string
    {
        return $this->getAdapter($adapter)->url($path);
    }
}
