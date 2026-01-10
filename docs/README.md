# MAAF Storage Dokumentáció

## Áttekintés

MAAF Storage egy fájlkezelési rendszer asset pipeline-lel, CDN integrációval és modulonkénti storage namespace-szel.

## Funkciók

- ✅ **Fájlkezelés** - Storage adapterek (Local, S3, stb.)
- ✅ **Asset Pipeline** - Asset kezelés és optimalizálás
- ✅ **CDN Integráció** - CDN támogatás
- ✅ **Modulonkénti Namespace** - Modul szintű storage namespace
- ✅ **CLI Támogatás** - Storage kezelés CLI parancsokkal

## Telepítés

```bash
composer require maaf/storage
```

## Használat

### Alapvető Használat

```php
use MAAF\Storage\StorageManager;
use MAAF\Storage\Adapters\LocalStorageAdapter;

// Create storage manager
$storageManager = new StorageManager();

// Register adapter
$adapter = new LocalStorageAdapter('storage/app', 'http://example.com/storage');
$storageManager->registerAdapter('local', $adapter);
$storageManager->setDefaultAdapter('local');

// Put file
$storageManager->put('file.txt', 'Hello World');

// Get file
$content = $storageManager->get('file.txt');

// Get URL
$url = $storageManager->url('file.txt');
```

### Modulonkénti Namespace

```php
use MAAF\Storage\Namespace\ModuleStorageNamespace;

$namespace = new ModuleStorageNamespace($storageManager);

// Register namespace
$namespace->register('UserModule', 'modules/users');

// Use module namespace
$namespace->put('UserModule', 'avatar.jpg', $imageData);
$url = $namespace->url('UserModule', 'avatar.jpg');
```

### Asset Pipeline

```php
use MAAF\Storage\Asset\AssetPipeline;

$pipeline = new AssetPipeline($storageManager);

// Add processor
$pipeline->addProcessor(function($content, $extension) {
    if ($extension === 'css') {
        // Minify CSS
        return preg_replace('/\s+/', ' ', $content);
    }
    return $content;
});

// Process asset
$processedPath = $pipeline->process('source.css', 'compiled.css');
```

### CDN Integráció

```php
use MAAF\Storage\CDN\CDNManager;
use MAAF\Storage\CDN\CloudflareCDNAdapter;

$cdnManager = new CDNManager($storageManager);

// Register CDN
$cloudflare = new CloudflareCDNAdapter('zone-id', 'api-token', 'https://cdn.example.com');
$cdnManager->register('cloudflare', $cloudflare);
$cdnManager->setDefault('cloudflare');

// Upload to CDN
$cdnUrl = $cdnManager->upload('local/file.jpg', 'cdn/file.jpg');

// Invalidate cache
$cdnManager->invalidate(['cdn/file.jpg']);
```

## CLI Parancsok

```bash
# Publish file
php maaf storage:publish source.txt storage/destination.txt

# Compile assets
php maaf asset:compile source1.css,source2.css compiled.css
```

## További információk

- [API Dokumentáció](api.md)
- [Példák](examples.md)
- [Best Practices](best-practices.md)
