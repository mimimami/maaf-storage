<?php

declare(strict_types=1);

namespace MAAF\Storage\CDN;

use MAAF\Storage\StorageManager;

/**
 * CDN Manager
 * 
 * CDN integráció kezelés.
 * 
 * @version 1.0.0
 */
final class CDNManager
{
    /**
     * @var array<string, CDNAdapterInterface>
     */
    private array $adapters = [];

    private ?string $defaultCDN = null;

    public function __construct(
        private readonly StorageManager $storageManager
    ) {
    }

    /**
     * Register CDN adapter
     * 
     * @param string $name CDN name
     * @param CDNAdapterInterface $adapter CDN adapter
     * @return void
     */
    public function register(string $name, CDNAdapterInterface $adapter): void
    {
        $this->adapters[$name] = $adapter;
    }

    /**
     * Set default CDN
     * 
     * @param string $name CDN name
     * @return void
     */
    public function setDefault(string $name): void
    {
        if (!isset($this->adapters[$name])) {
            throw new \RuntimeException("CDN adapter not found: {$name}");
        }

        $this->defaultCDN = $name;
    }

    /**
     * Upload file to CDN
     * 
     * @param string $localPath Local file path
     * @param string $cdnPath CDN file path
     * @param string|null $cdnName CDN name (null = default)
     * @return string CDN URL
     */
    public function upload(string $localPath, string $cdnPath, ?string $cdnName = null): string
    {
        $cdn = $this->getCDN($cdnName);
        $content = $this->storageManager->get($localPath);
        
        return $cdn->upload($cdnPath, $content);
    }

    /**
     * Invalidate CDN cache
     * 
     * @param string|array<int, string> $paths Paths to invalidate
     * @param string|null $cdnName CDN name (null = default)
     * @return void
     */
    public function invalidate(string|array $paths, ?string $cdnName = null): void
    {
        $cdn = $this->getCDN($cdnName);
        $paths = is_array($paths) ? $paths : [$paths];
        $cdn->invalidate($paths);
    }

    /**
     * Get CDN URL
     * 
     * @param string $path File path
     * @param string|null $cdnName CDN name (null = default)
     * @return string
     */
    public function url(string $path, ?string $cdnName = null): string
    {
        $cdn = $this->getCDN($cdnName);
        return $cdn->url($path);
    }

    /**
     * Get CDN adapter
     * 
     * @param string|null $name CDN name (null = default)
     * @return CDNAdapterInterface
     */
    private function getCDN(?string $name = null): CDNAdapterInterface
    {
        $name = $name ?? $this->defaultCDN;

        if ($name === null) {
            throw new \RuntimeException("No CDN configured");
        }

        if (!isset($this->adapters[$name])) {
            throw new \RuntimeException("CDN adapter not found: {$name}");
        }

        return $this->adapters[$name];
    }
}
