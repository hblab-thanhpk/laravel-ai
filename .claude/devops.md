# DevOps Instructions

## Docker Compose Standards
- Luôn bao gồm 4 services chính: `app` (php-fpm), `web` (nginx), `db` (postgres), `redis`.
- Sử dụng `Alpine` làm base image để tối ưu dung lượng.
- Các biến môi trường phải khớp với file `.env.example`.

## GitHub Actions Standards
- Workflow phải có bước: `Checkout`, `Setup PHP`, `Install Dependencies`, `Run Pint` (linting), `Run Tests` (Pest).
- Chỉ cho phép merge khi tất cả các test đã pass.
- Deploy qua SSH sử dụng `appleboy/ssh-action`.
