# Database & Eloquent Rules

- **PostgreSQL:** Luôn ưu tiên dùng `jsonb` cho các cột dữ liệu không cấu trúc.
- **Migrations:** Phải có đầy đủ `down()` method và các chỉ mục (indexes) cần thiết cho hiệu suất.
- **Eloquent:** - Không sử dụng `protected $guarded = []`.
    - Luôn định nghĩa `casts` cho các trường ngày tháng hoặc boolean.
    - Sử dụng `Lazy Loading` prevention trong môi trường local.
