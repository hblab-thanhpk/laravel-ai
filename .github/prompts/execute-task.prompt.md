---
mode: agent
description: "Thực hiện task theo đúng kiến trúc và rules của hệ thống"
---

## Yêu cầu
`$ARGUMENTS`

---

## Bước 1: Đọc Reference Files

Trước khi bắt đầu, **BẮT BUỘC** đọc các file sau theo thứ tự:

1. `.github/copilot/copilot-context.md` — Domain knowledge, business flows, system rules.
2. `.github/copilot/architecture.md` — Clean Architecture rules, coding standards, naming.
3. `.github/copilot/admin-rules.md` — Nếu task liên quan đến Admin Panel (Blade views).
4. `.github/copilot/api-rules.md` — Nếu task liên quan đến REST API.
5. `.github/copilot/database.md` — Nếu task liên quan đến migration, model, hoặc query.

---

## Bước 2: Phân loại Task

Xác định scope của task:
- [ ] **Admin Panel** — Blade views, web routes, session auth
- [ ] **REST API** — JSON routes, Sanctum token auth
- [ ] **Cả hai**
- [ ] **Infrastructure only** — migration, seeder, job, queue

---

## Bước 3: Lập kế hoạch với todos

Tạo danh sách công việc cụ thể theo đúng thứ tự phụ thuộc:

### Infrastructure (nếu cần)
- [ ] Migration (UUID, indexes — theo `database.md`)
- [ ] Model (HasUuids, fillable, casts, relationships)
- [ ] Seeder (nếu cần dữ liệu mẫu)

### Business Logic
- [ ] DTO (khi data có nhiều trường hoặc logic phức tạp)
- [ ] Service (business logic + transaction)
- [ ] Action (logic nhỏ tái sử dụng)
- [ ] Repository (chỉ khi query phức tạp)

### Interface
- [ ] FormRequest (validation rules)
- [ ] Controller (Admin: trả View | API: trả JsonResponse)
- [ ] View / API Resource (Blade templates hoặc Laravel Resource)
- [ ] Routes (đặt tên theo convention trong `admin-rules.md` hoặc `api-rules.md`)

### Quality
- [ ] Pest Feature test (happy path + edge cases chính)
- [ ] Chạy `./vendor/bin/pint` để format code

---

## Quy tắc thực thi

- **KHÔNG** giải thích lại rules đã có trong file `.md` — chỉ áp dụng.
- Ưu tiên khi mâu thuẫn: `architecture.md` > `api-rules.md` / `admin-rules.md` > `copilot-context.md`.
- Tập trung vào **logic thực thi** của task hiện tại, không thêm feature ngoài yêu cầu.

## Trả lời bằng Tiếng Việt
