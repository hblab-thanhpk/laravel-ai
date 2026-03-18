# Project Context - E-commerce System (Advanced)

## Overview

Đây là hệ thống bán hàng (E-commerce) gồm 2 phần chính:

1. Admin Panel (Laravel Blade)
2. Public API (cho Frontend SPA / Mobile)

---

## Core Domains

### 1. User Management

#### Mô tả

Quản lý người dùng và phân quyền

#### Chức năng

* CRUD User
* Gán Role
* Gán Permission
* Khóa / mở tài khoản

#### Roles

* admin: toàn quyền
* staff: quản lý sản phẩm, đơn hàng
* customer: người mua hàng (chỉ dùng API)

---

### 2. Product Management

#### Thuộc tính

* name
* price
* stock
* description

#### Mở rộng (optional)

* category
* images
* variants (size, color)

#### Logic

* Giá không âm
* Stock >= 0

---

### 3. Inventory Management

#### Mô tả

Quản lý tồn kho sản phẩm

#### Hành vi

* Tăng stock khi nhập hàng
* Giảm stock khi tạo đơn
* Không cho phép stock âm

---

### 4. Order Management

#### Thuộc tính

* user_id
* total_price
* status

#### Order Status

* pending
* paid
* shipped
* completed
* cancelled

---

## Business Flow

### 1. Create Order

Flow:

1. Validate request
2. Kiểm tra stock
3. Tạo Order
4. Tạo Order Items
5. Trừ stock
6. Dispatch Job (email / notification)

---

### 2. Cancel Order

Flow:

1. Kiểm tra trạng thái
2. Update status = cancelled
3. Hoàn lại stock

---

### 3. Update Order Status

* pending → paid
* paid → shipped
* shipped → completed

Không cho phép:

* completed → pending

---

## Data Relationships

* User hasMany Orders
* Order hasMany OrderItems
* Product hasMany OrderItems

---

## System Rules

* Tất cả ID dùng UUID
* Không cho phép stock âm
* Không cho phép order nếu thiếu hàng

---

## Admin vs API

### Admin

* Dùng Blade
* CRUD data
* Có UI

### API

* JSON only
* Không render view
* Dùng cho frontend riêng

---

## Performance Considerations

* Dùng eager loading khi cần
* Tránh N+1 query
* Dùng cache cho dữ liệu đọc nhiều
* API Auth dùng Sanctum

---

## Notes for Copilot

Khi generate code:

* Hiểu rõ domain (user, product, order)
* Không viết logic sai business flow
* Luôn kiểm tra stock khi tạo order
* Luôn handle transaction khi tạo order
