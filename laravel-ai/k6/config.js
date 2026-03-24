/**
 * Cấu hình chung cho tất cả k6 scripts.
 * Chỉnh BASE_URL trước khi chạy nếu cần.
 */
export const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';

export const DEFAULT_HEADERS = {
  'Content-Type': 'application/json',
  'Accept': 'application/json',
};

/**
 * Đăng nhập và trả về Bearer token.
 * Dùng chung ở tất cả scripts.
 */
export function login(http, email = 'loadtest_1@loadtest.local', password = 'password') {
  const res = http.post(
    `${BASE_URL}/api/auth/login`,
    JSON.stringify({ email, password }),
    { headers: DEFAULT_HEADERS },
  );

  if (res.status !== 200) {
    console.error(`Login failed [${res.status}]: ${res.body}`);
    return null;
  }

  return res.json('data.access_token');
}

/**
 * Thresholds chuẩn dùng chung — override trong từng scenario nếu cần.
 */
export const DEFAULT_THRESHOLDS = {
  // 95% requests dưới 500ms
  http_req_duration: ['p(95)<500'],
  // Tỉ lệ lỗi dưới 1%
  http_req_failed: ['rate<0.01'],
};
