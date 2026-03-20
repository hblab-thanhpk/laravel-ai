/**
 * SMOKE TEST — Kiểm tra hệ thống có hoạt động không với tải tối thiểu.
 *
 * Mục đích : Phát hiện lỗi cơ bản (500, timeout) trước khi chạy load test.
 * VUs       : 1
 * Thời gian : 30s
 *
 * Chạy:
 *   k6 run k6/smoke.js
 */

import http from 'k6/http';
import { check, sleep } from 'k6';
import { BASE_URL, DEFAULT_HEADERS, DEFAULT_THRESHOLDS, login } from './config.js';

export const options = {
  vus: 1,
  duration: '30s',
  thresholds: DEFAULT_THRESHOLDS,
};

// Chạy 1 lần trước toàn bộ test — lấy token dùng chung
let token;
export function setup() {
  token = login(http);
  return { token };
}

export default function (data) {
  const authHeaders = {
    ...DEFAULT_HEADERS,
    Authorization: `Bearer ${data.token}`,
  };

  // Rotate user theo iteration để không bị rate-limit cùng 1 email
  const userIndex = __ITER % 10_000 + 1;

  // 1. Login
  const loginRes = http.post(
    `${BASE_URL}/api/auth/login`,
    JSON.stringify({ email: `loadtest_${userIndex}@loadtest.local`, password: 'password' }),
    { headers: DEFAULT_HEADERS },
  );
  check(loginRes, {
    'login: status 200': (r) => r.status === 200,
    'login: có token': (r) => !!r.json('data.access_token'),
  });

  sleep(1);
}
