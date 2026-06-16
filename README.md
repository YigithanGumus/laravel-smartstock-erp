# 🏢 ERP Sistemi

Çok kiracılı (multi-tenant), API tabanlı, e-ticaret odaklı kurumsal kaynak planlama sistemi. Laravel ile geliştirilmiştir.

---

## 📋 İçindekiler

- [Genel Bakış](#genel-bakış)
- [Özellikler](#özellikler)
- [Teknolojiler](#teknolojiler)
- [Mimari](#mimari)
- [Klasör Yapısı](#klasör-yapısı)
- [Kurulum](#kurulum)
- [Docker](#docker)
- [Ortam Değişkenleri](#ortam-değişkenleri)
- [Veritabanı](#veritabanı)
- [API](#api)
- [Artisan Komutları](#artisan-komutları)
- [Geliştirme Rehberi](#geliştirme-rehberi)

---

## Genel Bakış

Bu sistem, birden fazla şirketin (tenant) aynı altyapı üzerinde bağımsız olarak çalışabildiği çok kiracılı bir ERP çözümüdür. Her şirketin verisi birbirinden izole edilmiştir. Yönetici olarak tüm şirket verilerine tek noktadan erişilebilir.

---

## Özellikler

- 🏢 **Multi-Tenant** — Her şirket kendi verisini görür, admin hepsini görür
- 🔐 **Kimlik Doğrulama** — API token, Passkey ve 2FA desteği
- 📦 **Ürün Yönetimi** — SKU, barkod, maliyet ve satış fiyatı takibi
- 🛒 **Sipariş Yönetimi** — Sipariş oluşturma, durum takibi, KDV hesaplama
- 🏭 **Depo & Stok** — Çoklu depo desteği, rezerve stok takibi
- 🔗 **Platform Entegrasyonu** — Trendyol, Shopify, WooCommerce gibi platformlarla bağlantı
- ⚙️ **Kuyruk & Cache** — Arka plan iş yönetimi

---

## Teknolojiler

| Teknoloji | Versiyon | Kullanım Amacı |
|---|---|---|
| PHP | 8.3+ | Temel dil |
| Laravel | 11.x | Framework |
| MySQL | 8.0 | Veritabanı |
| Redis | 7.x | Cache & Kuyruk |
| Docker | - | Konteyner |

---

## Mimari

Bu proje **Layered Architecture (Katmanlı Mimari)** kullanmaktadır. Her katmanın tek bir sorumluluğu vardır (SOLID - Single Responsibility).

```
Request → FormRequest → Controller → DTO → Service → Repository → Model
                                              ↓
                                          Event/Job (async)
```

### Katmanlar

| Katman | Sorumluluk |
|---|---|
| **FormRequest** | Gelen veriyi doğrular |
| **Controller** | İsteği alır, yönlendirir, cevabı döner |
| **DTO** | Veriyi katmanlar arası güvenli taşır |
| **Service** | İş mantığı (hesaplama, kontrol, kural) |
| **Repository** | Veritabanı sorguları |
| **Model** | Tablo tanımı ve ilişkiler |

> Her katman sadece bir altındakiyle konuşur, atlayamaz. Controller direkt Repository'e gidemez.

---

## Klasör Yapısı

```
app/
├── Console/
│   └── Commands/
│       └── MakeRepository.php        # php artisan make:repository
│
├── DTO/
│   ├── OrderDTO.php
│   └── ProductDTO.php
│
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   │           ├── OrderController.php
│   │           └── ProductController.php
│   ├── Requests/
│   └── Resources/
│
├── Models/
│   ├── Integration.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Passkey.php
│   ├── Product.php
│   ├── Tenant.php
│   ├── User.php
│   ├── Warehouse.php
│   └── WarehouseStock.php
│
├── Providers/
│   ├── AppServiceProvider.php
│   └── RepositoryServiceProvider.php # Otomatik oluşturulur
│
├── Repositories/
│   ├── BaseRepository/
│   │   ├── BaseRepository.php
│   │   └── Interfaces/
│   │       └── IBaseRepository.php
│   ├── OrderRepository/
│   │   ├── OrderRepository.php
│   │   └── Interfaces/
│   │       └── IOrderRepository.php
│   └── ProductRepository/
│       ├── ProductRepository.php
│       └── Interfaces/
│           └── IProductRepository.php
│
├── Services/
│   ├── OrderService.php
│   └── ProductService.php
│
└── Enums/
    ├── OrderStatus.php
    └── UserRole.php
```

---

## Kurulum

### Gereksinimler

- Docker & Docker Compose
- Git

### Adımlar

```bash
# Projeyi klonla
git clone <repo-url>
cd <proje-klasörü>

# Ortam dosyasını oluştur
cp .env.example .env

# Container'ları ayağa kaldır
docker compose up -d --build

# Bağımlılıkları yükle
docker compose exec app composer install

# Uygulama anahtarını oluştur
docker compose exec app php artisan key:generate

# Migration'ları çalıştır
docker compose exec app php artisan migrate

# Seeder çalıştır (opsiyonel)
docker compose exec app php artisan db:seed
```

---

## Docker

### Servisleri Başlatma

```bash
# İlk kurulum
docker compose up -d --build

# Sonraki başlatmalar
docker compose up -d
```

### Servisleri Durdurma

```bash
# Durdur
docker compose down

# Durdur ve tüm verileri sil
docker compose down -v
```

> ⚠️ `-v` parametresi veritabanı dahil tüm kalıcı verileri siler.

### Container'a Bağlanma

```bash
docker compose exec app bash
```

### Logları Görüntüleme

```bash
# Tüm servisler
docker compose logs -f

# Sadece uygulama
docker compose logs -f app

# Sadece veritabanı
docker compose logs -f mysql
```

### Dockerfile Değişikliği Sonrası

```bash
docker compose down
docker compose up -d --build
```

---

## Ortam Değişkenleri

```env
APP_NAME="ERP Sistemi"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PORT=6379
```

---

## Veritabanı

### Tablolar

| Tablo | Açıklama |
|---|---|
| `tenants` | Şirket kayıtları |
| `users` | Kullanıcılar |
| `passkeys` | Passkey kimlik doğrulama |
| `products` | Ürünler |
| `orders` | Siparişler |
| `order_items` | Sipariş satırları |
| `warehouses` | Depolar |
| `warehouse_stock` | Depo stok takibi |
| `integrations` | Platform entegrasyonları |
| `jobs` | Kuyruk işleri |
| `cache` | Önbellek |

### Migration

```bash
# Çalıştır
docker compose exec app php artisan migrate

# Sıfırla ve yeniden çalıştır
docker compose exec app php artisan migrate:fresh

# Seeder ile birlikte
docker compose exec app php artisan migrate:fresh --seed
```

### Multi-Tenant Yapısı

Tüm tenant'a ait tablolar `tenant_id` kolonu içerir. `TenantScope` global scope'u sayesinde her kullanıcı sadece kendi şirketinin verisini görür.

```php
// Normal kullanıcı — sadece kendi tenant'ını görür
Product::all(); // WHERE tenant_id = 1

// Admin — tüm tenant'ları görür
Product::withoutGlobalScope(TenantScope::class)->all();
```

---

## API

### Base URL

```
http://localhost/api/v1
```

### Kimlik Doğrulama

Tüm isteklerde `Authorization` header'ı gereklidir:

```
Authorization: Bearer {token}
```

### Endpoint'ler

#### Ürünler

```
GET    /api/v1/products          Ürün listesi
POST   /api/v1/products          Yeni ürün
GET    /api/v1/products/{id}     Ürün detayı
PUT    /api/v1/products/{id}     Ürün güncelle
DELETE /api/v1/products/{id}     Ürün sil
```

#### Siparişler

```
GET    /api/v1/orders            Sipariş listesi
POST   /api/v1/orders            Yeni sipariş
GET    /api/v1/orders/{id}       Sipariş detayı
PUT    /api/v1/orders/{id}       Sipariş güncelle
DELETE /api/v1/orders/{id}       Sipariş iptal
```

#### Depolar

```
GET    /api/v1/warehouses        Depo listesi
POST   /api/v1/warehouses        Yeni depo
GET    /api/v1/warehouses/{id}   Depo detayı
```

#### Entegrasyonlar

```
GET    /api/v1/integrations      Entegrasyon listesi
POST   /api/v1/integrations      Yeni entegrasyon
DELETE /api/v1/integrations/{id} Entegrasyon sil
```

---

## Artisan Komutları

### Repository Oluşturma

```bash
# Yeni repository oluştur
php artisan make:repository Product

# Çıktı:
# ✅ ProductRepository oluşturuldu.
# 📁 Repositories/ProductRepository/ProductRepository.php
# 📁 Repositories/ProductRepository/Interfaces/IProductRepository.php
# 🔗 RepositoryServiceProvider güncellendi.
```

```bash
# Mevcut repository'e ekstra dosya ekle
php artisan make:repository Product --add=ProductQuery

# Çıktı:
# ✅ ProductQueryRepository oluşturuldu.
# 📁 Repositories/ProductRepository/ProductQueryRepository.php
# 📁 Repositories/ProductRepository/Interfaces/IProductQueryRepository.php
```

### Diğer Komutlar

```bash
# Cache temizle
php artisan optimize:clear

# Route listesi
php artisan route:list

# Kuyruk işle
php artisan queue:work
```

---

## Geliştirme Rehberi

### Yeni Modül Ekleme

1. Migration oluştur

```bash
php artisan make:migration create_suppliers_table
```

2. Model oluştur

```bash
php artisan make:model Supplier
```

3. Repository oluştur

```bash
php artisan make:repository Supplier
```

4. DTO oluştur — `app/DTO/SupplierDTO.php`

5. Service oluştur — `app/Services/SupplierService.php`

6. Controller oluştur

```bash
php artisan make:controller Api/V1/SupplierController
```

### Kodlama Kuralları

- Controller içinde iş mantığı **yazılmaz**
- Service içinde direkt `Model::where()` **yazılmaz**, Repository kullanılır
- Her yeni tablo için `tenant_id` **eklenir**
- Hassas veriler (api_key, secret) **şifreli** saklanır

### Versiyon Yönetimi

API versiyonlama `V1`, `V2` klasörleriyle yönetilir. Yeni versiyon eklendiğinde mevcut endpoint'ler bozulmaz.

```
Http/Controllers/Api/
├── V1/
│   └── ProductController.php
└── V2/
    └── ProductController.php  ← yeni özellikler buraya
```
