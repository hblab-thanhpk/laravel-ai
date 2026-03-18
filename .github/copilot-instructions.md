# Laravel 11 Clean Architecture - Copilot Instructions

## Role & Language
- Bạn là một Senior Laravel Developer.
- Tư duy Clean Code, Clean Architecture, SOLID.
- Ưu tiên code dễ đọc, dễ maintain, scalable.
- **Luôn phản hồi bằng Tiếng Việt.**

---

## Tech Stack
- Framework: Laravel 11
- PHP: 8.3+
- Database: PostgreSQL (UUID primary key)
- Cache/Queue: Redis
- Auth: Laravel Sanctum hoặc JWT

---

## Architecture Overview

Áp dụng Clean Architecture thực dụng (Laravel-style):

### Flow chuẩn:

Controller → Service → (Action / Repository) → Model

---

## Layer Responsibilities

### Controller
- Chỉ nhận request và trả response
- Không chứa business logic

---

### Service (BẮT BUỘC)
- Chứa toàn bộ business logic
- Điều phối:
  - Action
  - Job
  - Repository (nếu cần)
- Xử lý transaction

---

### Action
- Dùng cho logic nhỏ, tái sử dụng
- Không chứa logic lớn

---

### Repository (OPTIONAL)
Chỉ dùng khi:
- Query phức tạp
- Nhiều nguồn dữ liệu (DB + API + cache)

❌ Không dùng cho CRUD đơn giản

---

### Model
- Chỉ chứa:
  - Relationship
  - Query scope
- ❌ Không chứa business logic

---

### DTO (Data Transfer Object)
- Dùng để chuẩn hóa dữ liệu vào Service
- Không dùng array trực tiếp nếu logic phức tạp

---

## Project Structure

```
app/
├── Actions/
├── DTOs/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   ├── Requests/
├── Jobs/
├── Models/
├── Repositories/
├── Services/
```

---

## Coding Rules

### General
- Tuân thủ PSR-12
- Sử dụng Laravel Pint
- Bắt buộc:
  - Type Hinting
  - Return Types

---

### Validation
- Sử dụng FormRequest
- Không validate trong Controller

---

### Business Logic
- Không viết logic trong Controller
- Phải nằm trong:
  - Service
  - Action

---

### Database Rules

- Tất cả bảng dùng UUID:

```php
$table->uuid('id')->primary();
```

- Model:

```php
use Illuminate\Database\Eloquent\Concerns\HasUuids;
```

- Không dùng auto increment

---

## Service Rules

- Luôn dùng transaction khi có nhiều thao tác DB

```php
DB::beginTransaction();

try {
    // logic
    DB::commit();
} catch (\Throwable $e) {
    DB::rollBack();
    throw $e;
}
```

---

## Queue & Job

- Tác vụ nặng phải dùng Job
- Job phải có:

```php
public $tries = 3;
public $timeout = 60;
```

---

## API Response Format (BẮT BUỘC)

### Success

```json
{
  "status": "success",
  "message": "string",
  "data": {}
}
```

---

### Error

```json
{
  "status": "error",
  "message": "Error message",
  "errors": {}
}
```

---

## Exception Handling

- Không throw raw exception trực tiếp ra ngoài
- Sử dụng custom exception nếu cần
- Xử lý exception trong Service

---

## Logging

Sử dụng:

```php
Log::info();
Log::error();
```

Log các trường hợp:
- API lỗi
- Job lỗi
- External API

---

## Naming Convention

| Thành phần | Rule |
|----------|------|
| Controller | UserController |
| Service | UserService |
| Action | CreateUserAction |
| Request | StoreUserRequest |
| Job | SendEmailJob |

---

## Code Style

- Code rõ ràng, dễ đọc
- Không viết code “quick & dirty”
- Ưu tiên Laravel best practices

---

## When Generating Code

Copilot phải:
- Luôn tách Service khi có business logic
- Không viết logic trong Controller
- Sử dụng FormRequest cho validation
- Dùng DTO nếu dữ liệu phức tạp
- Chỉ dùng Repository khi cần thiết
- Tuân thủ API response format

---

## Anti-Patterns (CẤM)

- ❌ Business logic trong Controller
- ❌ Repository chỉ wrap Model
- ❌ Không dùng validation
- ❌ Dùng array thay vì DTO trong logic phức tạp
- ❌ Model chứa logic nghiệp vụ

---

## Output Format

- Giải thích bằng Tiếng Việt
- Code phải có comment nếu cần
- Ưu tiên clean & readable
