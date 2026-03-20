# Clean Architecture Rules

## Layer Responsibilities

### Controller
- Chỉ nhận Request, gọi Service, trả Response hoặc View.
- Không chứa business logic.

### Service (BẮT BUỘC)
- Chứa toàn bộ business logic.
- Điều phối Action, Job, Repository.
- Xử lý DB transaction.

### Action
- Logic nhỏ, tái sử dụng được.
- Không chứa flow xử lý phức tạp.

### Repository (OPTIONAL)
Chỉ dùng khi:
- Query phức tạp (nhiều join, filter động).
- Nhiều nguồn dữ liệu (DB + Redis + External API).

❌ Không tạo Repository chỉ để wrap `Model::find()` hay CRUD đơn giản.

### Model
- Chỉ chứa: relationships, query scopes, casts.
- ❌ Không chứa business logic.

### DTO (Data Transfer Object)
- Dùng khi data vào Service có nhiều trường hoặc logic phức tạp.
- Không dùng raw `array` khi dùng DTO rõ ràng hơn.

---

## Project Structure

```
app/
├── Actions/
├── DTOs/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   └── Api/
│   ├── Middleware/
│   └── Requests/
├── Jobs/
├── Models/
├── Repositories/
└── Services/
    ├── Admin/
    └── Api/ (hoặc phân theo domain)
```

---

## Coding Standards

### General
- Tuân thủ PSR-12. Dùng `Laravel Pint` để format.
- Bắt buộc: **Type Hinting** và **Return Types** trên tất cả methods.

### Validation
- Luôn dùng **FormRequest**. Không validate trong Controller.

---

## Service — Transaction Pattern

Luôn dùng transaction khi có nhiều thao tác DB:

```php
DB::beginTransaction();
try {
    // ... logic
    DB::commit();
} catch (\Throwable $e) {
    DB::rollBack();
    throw $e;
}
```

---

## Queue & Jobs

- Tác vụ nặng / async phải dùng Job.
- Job phải khai báo:

```php
public int $tries = 3;
public int $timeout = 60;
```

---

## Exception Handling

- Không throw raw Exception ra ngoài Service.
- Dùng custom Exception class khi cần phân biệt lỗi nghiệp vụ.
- Xử lý và log exception trong Service, không để bubble lên Controller.

---

## Logging

```php
Log::info('context', ['data' => $data]);
Log::error('context', ['error' => $e->getMessage()]);
```

Bắt buộc log khi:
- External API lỗi
- Job thất bại
- Business rule bị vi phạm (ví dụ: stock âm, last admin bị demote)

---

## Naming Convention

| Thành phần | Convention | Ví dụ |
|------------|------------|-------|
| Controller | `{Resource}Controller` | `UserController` |
| Service | `{Resource}Service` | `UserService` |
| Action | `{Verb}{Resource}Action` | `CreateUserAction` |
| FormRequest | `{Verb}{Resource}Request` | `StoreUserRequest` |
| Job | `{Verb}{Resource}Job` | `SendWelcomeEmailJob` |
| DTO | `{Resource}DTO` | `CreateUserDTO` |
| Repository | `{Resource}Repository` | `OrderRepository` |

---

## Code Generation Checklist

Khi generate code, PHẢI đảm bảo:

- [ ] Service chứa toàn bộ business logic (không để trong Controller)
- [ ] FormRequest cho mọi form/API input
- [ ] DTO khi data phức tạp (nhiều trường, nhiều bước xử lý)
- [ ] Repository chỉ tạo khi query thực sự phức tạp
- [ ] Transaction khi có nhiều thao tác DB
- [ ] Type hint đầy đủ parameters và return types
- [ ] Đặt tên theo Naming Convention ở trên
- [ ] Pest test cho happy path + edge case chính
