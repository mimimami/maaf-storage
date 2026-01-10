# MAAF Storage Példák

## Alapvető Használat

### Storage Manager Setup

```php
use MAAF\Storage\StorageManager;
use MAAF\Storage\Adapters\LocalStorageAdapter;
use MAAF\Storage\Adapters\S3StorageAdapter;

// Create storage manager
$storageManager = new StorageManager();

// Register local adapter
$local = new LocalStorageAdapter('storage/app', 'http://example.com/storage');
$storageManager->registerAdapter('local', $local);

// Register S3 adapter
$s3 = new S3StorageAdapter(
    bucket: 'my-bucket',
    region: 'us-east-1',
    key: 'access-key',
    secret: 'secret-key',
    baseUrl: 'https://my-bucket.s3.amazonaws.com'
);
$storageManager->registerAdapter('s3', $s3);

// Set default adapter
$storageManager->setDefaultAdapter('local');
```

### Fájlkezelés

```php
// Put file
$storageManager->put('documents/file.txt', 'Hello World');

// Get file
$content = $storageManager->get('documents/file.txt');

// Check if exists
if ($storageManager->exists('documents/file.txt')) {
    echo "File exists\n";
}

// Get file URL
$url = $storageManager->url('documents/file.txt');

// Delete file
$storageManager->delete('documents/file.txt');

// Copy file
$storageManager->copy('source.txt', 'destination.txt');

// Move file
$storageManager->move('old.txt', 'new.txt');
```

## Modulonkénti Storage Namespace

### Namespace Regisztráció

```php
use MAAF\Storage\Namespace\ModuleStorageNamespace;

$namespace = new ModuleStorageNamespace($storageManager);

// Register namespaces for modules
$namespace->register('UserModule', 'modules/users');
$namespace->register('ProductModule', 'modules/products');
$namespace->register('MediaModule', 'modules/media');
```

### Modul Fájlok Kezelése

```php
// Put file in module namespace
$namespace->put('UserModule', 'avatars/user-1.jpg', $imageData);

// Get file from module namespace
$avatar = $namespace->get('UserModule', 'avatars/user-1.jpg');

// Get URL
$avatarUrl = $namespace->url('UserModule', 'avatars/user-1.jpg');

// Check if exists
if ($namespace->exists('UserModule', 'avatars/user-1.jpg')) {
    echo "Avatar exists\n";
}
```

### Automatikus Path Kezelés

```php
// Files are automatically stored in module namespace
// UserModule -> modules/users/avatars/user-1.jpg
// ProductModule -> modules/products/images/product-1.jpg

$namespace->put('UserModule', 'avatars/user-1.jpg', $data);
// Stored at: modules/users/avatars/user-1.jpg

$namespace->put('ProductModule', 'images/product-1.jpg', $data);
// Stored at: modules/products/images/product-1.jpg
```

## Asset Pipeline

### Pipeline Setup

```php
use MAAF\Storage\Asset\AssetPipeline;

$pipeline = new AssetPipeline($storageManager, 'public/assets');

// Add CSS minifier
$pipeline->addProcessor(function($content, $extension) {
    if ($extension === 'css') {
        // Remove comments
        $content = preg_replace('/\/\*.*?\*\//s', '', $content);
        // Remove whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        return trim($content);
    }
    return $content;
});

// Add JS minifier
$pipeline->addProcessor(function($content, $extension) {
    if ($extension === 'js') {
        // Simple minification (in production, use proper minifier)
        $content = preg_replace('/\s+/', ' ', $content);
        return trim($content);
    }
    return $content;
});
```

### Asset Feldolgozás

```php
// Process single asset
$processedPath = $pipeline->process(
    sourcePath: 'resources/css/app.css',
    destinationPath: 'public/assets/app.css'
);

// Process and version asset
$versionedPath = $pipeline->processAndVersion(
    sourcePath: 'resources/css/app.css',
    destinationPath: 'public/assets/app.css'
);
// Result: public/assets/app.a1b2c3d4.css

// Compile multiple assets
$compiledPath = $pipeline->compile(
    sources: [
        'resources/css/reset.css',
        'resources/css/base.css',
        'resources/css/components.css',
    ],
    destination: 'public/assets/compiled.css'
);
```

### Versioning

```php
// Assets are automatically versioned with hash
$versionedPath = $pipeline->processAndVersion('source.css', 'compiled.css');
// Generated: compiled.a1b2c3d4.css

// Use in templates
$url = $storageManager->url($versionedPath);
// <link rel="stylesheet" href="/assets/compiled.a1b2c3d4.css">
```

## CDN Integráció

### CDN Setup

```php
use MAAF\Storage\CDN\CDNManager;
use MAAF\Storage\CDN\CloudflareCDNAdapter;

$cdnManager = new CDNManager($storageManager);

// Register Cloudflare CDN
$cloudflare = new CloudflareCDNAdapter(
    zoneId: 'your-zone-id',
    apiToken: 'your-api-token',
    baseUrl: 'https://cdn.example.com'
);
$cdnManager->register('cloudflare', $cloudflare);
$cdnManager->setDefault('cloudflare');
```

### CDN Használat

```php
// Upload file to CDN
$cdnUrl = $cdnManager->upload(
    localPath: 'storage/app/images/logo.png',
    cdnPath: 'images/logo.png'
);
// Returns: https://cdn.example.com/images/logo.png

// Get CDN URL
$url = $cdnManager->url('images/logo.png');

// Invalidate cache
$cdnManager->invalidate(['images/logo.png']);

// Invalidate multiple files
$cdnManager->invalidate([
    'images/logo.png',
    'images/banner.jpg',
    'css/app.css',
]);
```

### Automatikus CDN Upload

```php
// Upload to storage first
$storageManager->put('images/logo.png', $imageData);

// Then upload to CDN
$cdnUrl = $cdnManager->upload('images/logo.png', 'images/logo.png');

// Use CDN URL in application
echo $cdnUrl; // https://cdn.example.com/images/logo.png
```

## Teljes Példa

### Setup és Használat

```php
use MAAF\Storage\StorageManager;
use MAAF\Storage\Adapters\LocalStorageAdapter;
use MAAF\Storage\Namespace\ModuleStorageNamespace;
use MAAF\Storage\Asset\AssetPipeline;
use MAAF\Storage\CDN\CDNManager;
use MAAF\Storage\CDN\CloudflareCDNAdapter;

// 1. Setup storage manager
$storageManager = new StorageManager();
$local = new LocalStorageAdapter('storage/app', 'http://example.com/storage');
$storageManager->registerAdapter('local', $local);
$storageManager->setDefaultAdapter('local');

// 2. Setup module namespace
$namespace = new ModuleStorageNamespace($storageManager);
$namespace->register('UserModule', 'modules/users');

// 3. Setup asset pipeline
$pipeline = new AssetPipeline($storageManager, 'public/assets');
$pipeline->addProcessor(fn($content, $ext) => $ext === 'css' ? preg_replace('/\s+/', ' ', $content) : $content);

// 4. Setup CDN
$cdnManager = new CDNManager($storageManager);
$cloudflare = new CloudflareCDNAdapter('zone-id', 'token', 'https://cdn.example.com');
$cdnManager->register('cloudflare', $cloudflare);

// 5. Store file in module namespace
$namespace->put('UserModule', 'avatars/user-1.jpg', $imageData);

// 6. Process asset
$processedPath = $pipeline->process('resources/css/app.css', 'public/assets/app.css');

// 7. Upload to CDN
$cdnUrl = $cdnManager->upload($processedPath, 'assets/app.css');
```

## CLI Használat

```bash
# Publish file to storage
php maaf storage:publish source.txt storage/destination.txt

# Compile assets
php maaf asset:compile source1.css,source2.css compiled.css

# Compile with versioning
php maaf asset:compile app.css,base.css app.compiled.css
```

## Best Practices

### Storage Adapter Választás

```php
// Local: Development, small files
$local = new LocalStorageAdapter('storage/app');

// S3: Production, large files, scalability
$s3 = new S3StorageAdapter('my-bucket', 'us-east-1');

// Use different adapters for different purposes
$storageManager->registerAdapter('local', $local);
$storageManager->registerAdapter('s3', $s3);

// Use S3 for user uploads
$storageManager->put('uploads/file.jpg', $data, [], 's3');

// Use local for temporary files
$storageManager->put('temp/file.txt', $data, [], 'local');
```

### Module Namespace Használat

```php
// Always use module namespace for module-specific files
$namespace->put('UserModule', 'avatars/user-1.jpg', $data);

// This ensures:
// - Files are organized by module
// - No conflicts between modules
// - Easy to backup/restore per module
```

### Asset Pipeline Optimalizálás

```php
// 1. Compile assets in production
if ($environment === 'production') {
    $pipeline->compile(['app.css', 'base.css'], 'compiled.css');
}

// 2. Use versioning for cache busting
$versionedPath = $pipeline->processAndVersion('app.css', 'app.css');

// 3. Minify in production
if ($environment === 'production') {
    $pipeline->addProcessor(function($content, $ext) {
        return $ext === 'css' ? minify($content) : $content;
    });
}
```

### CDN Stratégia

```php
// 1. Upload static assets to CDN
$cdnUrl = $cdnManager->upload('assets/app.css', 'assets/app.css');

// 2. Invalidate cache on update
$pipeline->process('app.css', 'app.css');
$cdnManager->invalidate(['assets/app.css']);

// 3. Use CDN for user uploads
$cdnUrl = $cdnManager->upload('uploads/user-1.jpg', 'uploads/user-1.jpg');
```
