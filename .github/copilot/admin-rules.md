# Admin Rules — Laravel Blade (SSR)

## Overview
- Admin sử dụng Laravel Blade (Server-Side Rendering).
- Không dùng SPA trong admin.
- Mục tiêu: CRUD + quản lý hệ thống.

---

## Admin Modules
- User Management (+ Role & Permission)
- Product Management (+ Category, Variant)
- Inventory Management
- Order Management

---

## Controller Rules

- Controller trả về view, không chứa business logic.
- Gọi Service để xử lý.

```php
public function index(): View
{
    $users = $this->userService->paginate();
    return view('admin.users.index', compact('users'));
}
```

---

## View Structure

```
resources/views/
└── admin/
    ├── layouts/
    │   └── app.blade.php
    ├── components/
    ├── users/
    ├── roles/
    ├── permissions/
    ├── categories/
    ├── products/
    └── orders/
```

---

## Layout Rules

Tất cả page phải extend layout:

```blade
@extends('admin.layouts.app')
@section('content')
    ...
@endsection
```

---

## UI Standards

### Index Page
- Hiển thị table với: pagination, search, filter.

### Create / Edit Page
- Dùng form với validation error display.

---

## Form Rules

```blade
<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    ...
</form>
```

Update phải thêm:
```blade
@method('PUT')
```

---

## Validation Error Display

```blade
@error('name')
    <div class="text-danger">{{ $message }}</div>
@enderror
```

---

## Route Naming Convention

| Action  | Route Name             |
|---------|------------------------|
| index   | admin.users.index      |
| create  | admin.users.create     |
| store   | admin.users.store      |
| show    | admin.users.show       |
| edit    | admin.users.edit       |
| update  | admin.users.update     |
| destroy | admin.users.destroy    |

---

## Authorization

- Admin Auth dùng Session (Laravel default).
- Dùng middleware `permission` để giới hạn theo action.
- Unauthorized → redirect về `admin.dashboard` với flash error.

---

## Pagination

```blade
{{ $users->links() }}
```

---

## Flash Message

```blade
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
```

---

## Security — XSS

- Luôn dùng `{{ }}` để escape output.
- ❌ Không dùng `{!! !!}` trừ khi render HTML đã được sanitize.

---

## Anti-Patterns (CẤM trong Admin)

- ❌ Query DB trong Blade template
- ❌ Logic xử lý trong View
- ❌ Page không extend layout
- ❌ Form không có `@csrf`
- ❌ Dùng `{!! !!}` vô tội vạ (XSS)
- ❌ Data không được truyền từ Controller (query trực tiếp trong view)
