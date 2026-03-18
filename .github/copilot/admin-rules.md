# Admin Rules - Laravel Blade (Advanced)

## Overview

* Admin sử dụng Laravel Blade (SSR)
* Không dùng SPA trong admin
* Mục tiêu: CRUD + quản lý hệ thống

---

## Admin Modules

* User Management
* Role & Permission
* Product Management
* Inventory Management
* Order Management

---

## Controller Rules

* Controller trả về view
* Không chứa business logic
* Gọi Service để xử lý

Ví dụ:

```php
public function index()
{
    $users = $this->userService->paginate();
    return view('admin.users.index', compact('users'));
}
```

---

## View Structure

resources/views/
├── admin/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── components/
│   ├── users/
│   ├── products/
│   ├── orders/

---

## Layout Rules

* Tất cả page phải extend layout:

```blade
@extends('admin.layouts.app')
```

---

## UI Standards

### 1. Index Page

* Hiển thị table
* Có:

  * pagination
  * search
  * filter

---

### 2. Create / Edit Page

* Dùng form
* Hiển thị validation error

---

## Blade Component Usage

* Tái sử dụng component:

Ví dụ:

* input
* button
* table

---

## Form Rules

```blade
<form method="POST" action="">
    @csrf
</form>
```

* Update:

```blade
@method('PUT')
```

---

## Validation Error

```blade
@error('name')
    <div class="text-danger">{{ $message }}</div>
@enderror
```

---

## Route Naming Convention

| Action  | Route               |
| ------- | ------------------- |
| index   | admin.users.index   |
| create  | admin.users.create  |
| store   | admin.users.store   |
| edit    | admin.users.edit    |
| update  | admin.users.update  |
| destroy | admin.users.destroy |

---

## Authorization

* Admin Auth dùng Session (Laravel default)
* Dùng middleware hoặc policy
* Ví dụ:

  * chỉ admin mới quản lý user

---

## Data Handling

* Không query DB trong blade
* Data phải được truyền từ controller

---

## Pagination

```blade
{{ $users->links() }}
```

---

## Flash Message

```blade
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
```

---

## Anti-pattern (CẤM)

* ❌ Query DB trong Blade
* ❌ Logic trong view
* ❌ Không dùng layout
* ❌ Form không có CSRF

---

## When Generating Admin Code

Copilot PHẢI:

* Tạo Controller + Service
* Tạo view theo structure
* Dùng layout
* Tạo form đúng chuẩn
* Có validation error
* Có pagination
* Code phải clean, dễ đọc

## Security - XSS

- Luôn dùng {{ }} để escape
- ❌ Không dùng {!! !!} trừ khi cần thiết
