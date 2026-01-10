<?php

declare(strict_types=1);

namespace MAAF\Storage\CLI\Commands;

use MAAF\Core\Cli\CommandInterface;
use MAAF\Storage\Asset\AssetPipeline;

/**
 * Asset Compile Command
 * 
 * Asset-ek fordítása.
 * 
 * @version 1.0.0
 */
final class AssetCompileCommand implements CommandInterface
{
    public function __construct(
        private readonly ?AssetPipeline $pipeline = null
    ) {
    }

    public function getName(): string
    {
        return 'asset:compile';
    }

    public function getDescription(): string
    {
        return 'Compile assets';
    }

    public function execute(array $args): int
    {
        if ($this->pipeline === null) {
            echo "❌ Asset pipeline not available\n";
            return 1;
        }

        $sources = explode(',', $args[0] ?? '');
        $destination = $args[1] ?? null;

        if (empty($sources) || $destination === null) {
            echo "❌ Sources and destination required\n";
            echo "Usage: php maaf asset:compile <source1,source2,...> <destination>\n";
            return 1;
        }

        echo "Compiling assets...\n";
        $this->pipeline->compile($sources, $destination);

        echo "✅ Compiled: " . implode(', ', $sources) . " -> {$destination}\n";
        return 0;
    }
}
