/**
 * STRESS TEST — Tìm giới hạn chịu tải tối đa (breaking point).
 *
 * Mục đích  : Tăng tải dần đến khi hệ thống bắt đầu fail hoặc latency vượt ngưỡng.
 * VUs peak  : 200
 * Thời gian : ~7 phút
 *
 * Chạy:
 *   k6 run k6/stress.js
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';
import { BASE_URL, DEFAULT_HEADERS } from './config.js';

const loginSuccessRate  = new Rate('login_success_rate');
const loginDuration     = new Trend('login_duration_ms');

export const options = {
  stages: [
    { duration: '30s', target: 10  },
    { duration: '60s', target: 50  },
    { duration: '60s', target: 100 },
    { duration: '60s', target: 150 },
    { duration: '60s', target: 200 }, // Peak — tìm breaking point
    { duration: '60s', target: 200 }, // Giữ ở peak
    { duration: '30s', target: 0   }, // Recovery
  ],
  thresholds: {
    // Stress test: nới rộng ngưỡng hơn load test
    'http_req_duration': ['p(95)<2000'],
    'http_req_failed':   ['rate<0.05'],
    'login_success_rate': ['rate>0.95'],
  },
};

export default function () {
  const userIndex = (__VU - 1) % 10_000 + 1;

  group('auth: login', () => {
    const res = http.post(
      `${BASE_URL}/api/auth/login`,
      JSON.stringify({ email: `loadtest_${userIndex}@loadtest.local`, password: 'password' }),
      { headers: DEFAULT_HEADERS, tags: { name: 'POST /api/auth/login' } },
    );

    loginSuccessRate.add(res.status === 200);
    loginDuration.add(res.timings.duration);

    check(res, {
      'login: 200 OK':        (r) => r.status === 200,
      'login: có token':      (r) => !!r.json('data.access_token'),
      'login: dưới 2s':       (r) => r.timings.duration < 2000,
    });

    if (res.status === 200) {
      const token = res.json('data.access_token');
      sleep(0.3);

      // Logout ngay để giải phóng session token
      http.post(
        `${BASE_URL}/api/auth/logout`,
        null,
        {
          headers: { ...DEFAULT_HEADERS, Authorization: `Bearer ${token}` },
          tags: { name: 'POST /api/auth/logout' },
        },
      );
    }
  });

  sleep(0.5);
}
