# Database & Eloquent Rules

## UUID Primary Key (BẮT BUỘC)

Tất cả bảng phải dùng UUID thay vì auto-increment:

```php
// Migration
$table->uuid('id')->primary();
```

```php
// Model
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Model
{
    use HasUuids;
    // ...
}
```

❌ Không dùng `$table->id()` (auto-increment integer).

---

## PostgreSQL Rules

- Dùng `jsonb` (thay vì `json`) cho cột dữ liệu không cấu trúc.
- Thêm index cho các cột dùng trong `WHERE`, `ORDER BY`, và foreign keys.

---

## Migration Rules

- Phải có đầy đủ `down()` method để rollback.
- Thêm indexes cần thiết ngay trong migration.

```php
$table->index(['user_id', 'status']);
```

---

## Eloquent Rules

- ❌ Không dùng `protected $guarded = []` — luôn khai báo `$fillable` tường minh.
- Luôn khai báo `casts` cho: dates, booleans, enums, JSON.
- Bật **Lazy Loading prevention** trong môi trường local:

```php
// AppServiceProvider::boot()
Model::preventLazyLoading(! app()->isProduction());
```
