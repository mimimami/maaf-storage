<?php

declare(strict_types=1);

namespace MAAF\Storage\CDN;

/**
 * Cloudflare CDN Adapter
 * 
 * Cloudflare CDN adapter implementáció.
 * 
 * @version 1.0.0
 */
final class CloudflareCDNAdapter implements CDNAdapterInterface
{
    public function __construct(
        private readonly string $zoneId,
        private readonly string $apiToken,
        private readonly string $baseUrl
    ) {
    }

    public function upload(string $path, string $content): string
    {
        // Note: Cloudflare doesn't have direct upload API
        // Files should be uploaded to origin server, then cached by CDN
        // This is a placeholder for cache invalidation
        
        return $this->url($path);
    }

    public function invalidate(array $paths): void
    {
        // Purge cache via Cloudflare API
        // $client = new \GuzzleHttp\Client();
        // $client->post("https://api.cloudflare.com/client/v4/zones/{$this->zoneId}/purge_cache", [
        //     'headers' => ['Authorization' => "Bearer {$this->apiToken}"],
        //     'json' => ['files' => $paths],
        // ]);
    }

    public function url(string $path): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
