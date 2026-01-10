<?php

declare(strict_types=1);

namespace MAAF\Storage\Asset;

use MAAF\Storage\StorageManager;

/**
 * Asset Pipeline
 * 
 * Asset pipeline kezelés és optimalizálás.
 * 
 * @version 1.0.0
 */
final class AssetPipeline
{
    /**
     * @var array<int, callable>
     */
    private array $processors = [];

    public function __construct(
        private readonly StorageManager $storageManager,
        private readonly string $publicPath = 'public/assets'
    ) {
    }

    /**
     * Add processor
     * 
     * @param callable $processor Processor callback
     * @return void
     */
    public function addProcessor(callable $processor): void
    {
        $this->processors[] = $processor;
    }

    /**
     * Process asset
     * 
     * @param string $sourcePath Source file path
     * @param string $destinationPath Destination file path
     * @param array<string, mixed> $options Options
     * @return string Processed file path
     */
    public function process(string $sourcePath, string $destinationPath, array $options = []): string
    {
        $content = $this->storageManager->get($sourcePath);
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);

        // Apply processors
        foreach ($this->processors as $processor) {
            $content = $processor($content, $extension, $options);
        }

        // Save processed file
        $this->storageManager->put($destinationPath, $content, $options);

        return $destinationPath;
    }

    /**
     * Process and version asset
     * 
     * @param string $sourcePath Source file path
     * @param string $destinationPath Destination file path
     * @param array<string, mixed> $options Options
     * @return string Versioned file path
     */
    public function processAndVersion(string $sourcePath, string $destinationPath, array $options = []): string
    {
        $processedPath = $this->process($sourcePath, $destinationPath, $options);
        
        // Add version hash
        $content = $this->storageManager->get($processedPath);
        $hash = substr(md5($content), 0, 8);
        
        $pathInfo = pathinfo($processedPath);
        $versionedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $hash . '.' . $pathInfo['extension'];
        
        $this->storageManager->move($processedPath, $versionedPath);
        
        return $versionedPath;
    }

    /**
     * Compile assets
     * 
     * @param array<int, string> $sources Source file paths
     * @param string $destination Destination file path
     * @param array<string, mixed> $options Options
     * @return string Compiled file path
     */
    public function compile(array $sources, string $destination, array $options = []): string
    {
        $compiled = '';

        foreach ($sources as $source) {
            $content = $this->storageManager->get($source);
            $compiled .= $content . "\n";
        }

        // Apply processors
        foreach ($this->processors as $processor) {
            $extension = pathinfo($destination, PATHINFO_EXTENSION);
            $compiled = $processor($compiled, $extension, $options);
        }

        $this->storageManager->put($destination, $compiled, $options);

        return $destination;
    }
}
