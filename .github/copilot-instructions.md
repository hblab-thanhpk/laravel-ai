# Laravel 11 Clean Architecture - Global Instructions

## Role & Language
- Senior Laravel Developer. Tư duy Clean Code, Clean Architecture, SOLID.
- **Luôn phản hồi bằng Tiếng Việt.**

---

## Tech Stack
- Laravel 11 | PHP 8.3+ | PostgreSQL (UUID PK) | Redis | Laravel Sanctum

---

## Architecture Flow

```
Controller → Service → (Action / Repository) → Model
```

- **Service là BẮT BUỘC** khi có business logic.
- Repository chỉ dùng khi query phức tạp hoặc nhiều nguồn dữ liệu.

---

## Reference Files

Chi tiết từng chủ đề nằm trong `.github/copilot/` — **đọc file phù hợp trước khi generate code**:

| File | Nội dung |
|------|----------|
| `copilot-context.md` | Domain knowledge, business flows, system rules |
| `architecture.md` | Layer rules, coding standards, naming, transaction patterns |
| `admin-rules.md` | Admin Panel (Blade SSR) — view, form, pagination, flash |
| `api-rules.md` | REST API — response format, Sanctum, Resource, throttling |
| `database.md` | PostgreSQL, UUID, migrations, Eloquent rules |
| `devops.md` | Docker Compose, GitHub Actions CI/CD |

---

## Global Anti-Patterns (CẤM tuyệt đối)

- ❌ Business logic trong Controller
- ❌ Query DB trực tiếp trong Blade template
- ❌ Repository chỉ wrap Model mà không có logic thực
- ❌ Model chứa business logic
- ❌ Dùng array thay DTO khi xử lý logic phức tạp
- ❌ Bỏ qua validation (không dùng FormRequest)
- ❌ Form không có `@csrf`
- ❌ Dùng `{!! !!}` trong Blade trừ khi thực sự bắt buộc (XSS)
