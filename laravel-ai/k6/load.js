/**
 * LOAD TEST — Đo RPS (Requests Per Second) dưới tải thực tế.
 *
 * Mục đích  : Đo throughput, latency p95/p99 khi hệ thống chịu tải bình thường.
 * Scenario  : Ramp up → sustain → ramp down
 * VUs peak  : 50 (đổi theo môi trường)
 * Thời gian : ~3 phút
 *
 * Chạy:
 *   k6 run k6/load.js
 *   k6 run --env BASE_URL=http://myserver k6/load.js
 *
 * Xem kết quả HTML:
 *   k6 run --out html=k6/results/load.html k6/load.js
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';
import { BASE_URL, DEFAULT_HEADERS, DEFAULT_THRESHOLDS } from './config.js';

// Custom metrics
const loginSuccessRate = new Rate('login_success_rate');
const loginDuration    = new Trend('login_duration_ms');
const logoutSuccessRate = new Rate('logout_success_rate');

export const options = {
  stages: [
    { duration: '30s', target: 10 },  // Ramp up → 10 VUs
    { duration: '60s', target: 50 },  // Ramp up → 50 VUs (peak)
    { duration: '60s', target: 50 },  // Sustain 50 VUs
    { duration: '30s', target: 0  },  // Ramp down
  ],
  thresholds: {
    ...DEFAULT_THRESHOLDS,
    'login_success_rate': ['rate>0.99'],
    'login_duration_ms': ['p(95)<800'],
  },
};

/**
 * Mỗi VU chạy vòng lặp: login → (gọi API auth) → logout → sleep.
 * Giả lập đúng flow của 1 user thực sự.
 */
export default function () {
  // Dùng email của loadtest users — index dựa theo VU id để phân tán
  const userIndex = (__VU - 1) % 10_000 + 1;
  const email = `loadtest_${userIndex}_`;  // prefix — server tìm LIKE

  // ── LOGIN ────────────────────────────────────────────────────────────────
  let token;

  group('auth: login', () => {
    // Lấy 1 user bất kỳ trong pool 10k users của load-test
    const vuEmail = `loadtest_${userIndex}_%`;

    const res = http.post(
      `${BASE_URL}/api/auth/login`,
      JSON.stringify({
        email: `loadtest_${userIndex}@loadtest.local`,
        password: 'password',
      }),
      { headers: DEFAULT_HEADERS, tags: { name: 'POST /api/auth/login' } },
    );

    loginSuccessRate.add(res.status === 200);
    loginDuration.add(res.timings.duration);

    check(res, {
      'login: 200 OK': (r) => r.status === 200,
      'login: body có token': (r) => !!r.json('data.access_token'),
    });

    if (res.status === 200) {
      token = res.json('data.access_token');
    }
  });

  if (!token) {
    sleep(1);
    return;
  }

  const authHeaders = { ...DEFAULT_HEADERS, Authorization: `Bearer ${token}` };

  sleep(0.5);

  // ── LOGOUT ───────────────────────────────────────────────────────────────
  group('auth: logout', () => {
    const res = http.post(
      `${BASE_URL}/api/auth/logout`,
      null,
      { headers: authHeaders, tags: { name: 'POST /api/auth/logout' } },
    );

    logoutSuccessRate.add(res.status === 200);

    check(res, {
      'logout: 200 OK': (r) => r.status === 200,
    });
  });

  sleep(1);
}
