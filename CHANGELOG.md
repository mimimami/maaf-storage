# Changelog

## [1.0.0] - 2024-01-XX

### Added

- ✅ **Storage Adapter Abstraction**
  - `StorageAdapterInterface` - Storage adapter interface
  - `LocalStorageAdapter` - Lokális fájlrendszer adapter
  - `S3StorageAdapter` - AWS S3 adapter
  - `StorageManager` - Storage kezelő több adapter támogatással

- ✅ **Module Storage Namespace**
  - `ModuleStorageNamespace` - Modulonkénti storage namespace kezelés
  - Namespace regisztráció
  - Modul szintű path kezelés

- ✅ **Asset Pipeline**
  - `AssetPipeline` - Asset pipeline kezelés és optimalizálás
  - Processor support
  - Asset compilation
  - Versioning support

- ✅ **CDN Integration**
  - `CDNManager` - CDN integráció kezelés
  - `CDNAdapterInterface` - CDN adapter interface
  - `CloudflareCDNAdapter` - Cloudflare CDN adapter
  - Cache invalidation

- ✅ **CLI Commands**
  - `StoragePublishCommand` - Fájlok publikálása storage-ba
  - `AssetCompileCommand` - Asset-ek fordítása

### Changed
- N/A (első kiadás)

### Fixed
- N/A (első kiadás)
