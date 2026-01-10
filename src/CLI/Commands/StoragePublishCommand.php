<?php

declare(strict_types=1);

namespace MAAF\Storage\CLI\Commands;

use MAAF\Core\Cli\CommandInterface;
use MAAF\Storage\StorageManager;

/**
 * Storage Publish Command
 * 
 * Fájlok publikálása storage-ba.
 * 
 * @version 1.0.0
 */
final class StoragePublishCommand implements CommandInterface
{
    public function __construct(
        private readonly ?StorageManager $storageManager = null
    ) {
    }

    public function getName(): string
    {
        return 'storage:publish';
    }

    public function getDescription(): string
    {
        return 'Publish files to storage';
    }

    public function execute(array $args): int
    {
        if ($this->storageManager === null) {
            echo "❌ Storage manager not available\n";
            return 1;
        }

        $source = $args[0] ?? null;
        $destination = $args[1] ?? null;

        if ($source === null || $destination === null) {
            echo "❌ Source and destination required\n";
            echo "Usage: php maaf storage:publish <source> <destination>\n";
            return 1;
        }

        if (!file_exists($source)) {
            echo "❌ Source file not found: {$source}\n";
            return 1;
        }

        $content = file_get_contents($source);
        $this->storageManager->put($destination, $content);

        echo "✅ Published: {$source} -> {$destination}\n";
        return 0;
    }
}
