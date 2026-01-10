<?php

declare(strict_types=1);

namespace MAAF\Storage\CDN;

/**
 * CDN Adapter Interface
 * 
 * CDN adapter interface.
 * 
 * @version 1.0.0
 */
interface CDNAdapterInterface
{
    /**
     * Upload file to CDN
     * 
     * @param string $path File path
     * @param string $content File content
     * @return string CDN URL
     */
    public function upload(string $path, string $content): string;

    /**
     * Invalidate cache
     * 
     * @param array<int, string> $paths Paths to invalidate
     * @return void
     */
    public function invalidate(array $paths): void;

    /**
     * Get CDN URL
     * 
     * @param string $path File path
     * @return string
     */
    public function url(string $path): string;
}
