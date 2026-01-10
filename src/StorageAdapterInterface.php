<?php

declare(strict_types=1);

namespace MAAF\Storage;

/**
 * Storage Adapter Interface
 * 
 * Storage adapter interface absztrakcióhoz.
 * 
 * @version 1.0.0
 */
interface StorageAdapterInterface
{
    /**
     * Check if file exists
     * 
     * @param string $path File path
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Get file contents
     * 
     * @param string $path File path
     * @return string
     */
    public function get(string $path): string;

    /**
     * Put file contents
     * 
     * @param string $path File path
     * @param string $contents File contents
     * @param array<string, mixed> $options Options
     * @return bool
     */
    public function put(string $path, string $contents, array $options = []): bool;

    /**
     * Delete file
     * 
     * @param string $path File path
     * @return bool
     */
    public function delete(string $path): bool;

    /**
     * Copy file
     * 
     * @param string $from Source path
     * @param string $to Destination path
     * @return bool
     */
    public function copy(string $from, string $to): bool;

    /**
     * Move file
     * 
     * @param string $from Source path
     * @param string $to Destination path
     * @return bool
     */
    public function move(string $from, string $to): bool;

    /**
     * Get file size
     * 
     * @param string $path File path
     * @return int
     */
    public function size(string $path): int;

    /**
     * Get file URL
     * 
     * @param string $path File path
     * @return string
     */
    public function url(string $path): string;

    /**
     * List files in directory
     * 
     * @param string $path Directory path
     * @param bool $recursive Recursive listing
     * @return array<int, string>
     */
    public function list(string $path, bool $recursive = false): array;
}
