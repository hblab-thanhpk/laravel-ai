/**
 * SPIKE TEST — Kiểm tra hệ thống khi tải tăng đột ngột rồi giảm ngay.
 *
 * Mục đích  : Giả lập traffic burst (flash sale, viral event).
 *             Kiểm tra hệ thống có recover sau spike không.
 * VUs peak  : 500 trong 10s
 * Thời gian : ~3 phút
 *
 * Chạy:
 *   k6 run k6/spike.js
 */

import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate } from 'k6/metrics';
import { BASE_URL, DEFAULT_HEADERS } from './config.js';

const successRate = new Rate('success_rate');

export const options = {
  stages: [
    { duration: '10s', target: 5   }, // Baseline bình thường
    { duration: '10s', target: 500 }, // SPIKE: tăng đột ngột → 500 VUs
    { duration: '10s', target: 500 }, // Giữ spike
    { duration: '10s', target: 5   }, // Drop về baseline
    { duration: '60s', target: 5   }, // Kiểm tra recovery
    { duration: '10s', target: 0   },
  ],
  thresholds: {
    // Spike test: chấp nhận latency cao hơn, nhưng hệ thống phải recover
    'http_req_duration': ['p(95)<5000'],
    'http_req_failed':   ['rate<0.10'],
    'success_rate':      ['rate>0.90'],
  },
};

export default function () {
  const userIndex = (__VU - 1) % 10_000 + 1;

  const res = http.post(
    `${BASE_URL}/api/auth/login`,
    JSON.stringify({ email: `loadtest_${userIndex}@loadtest.local`, password: 'password' }),
    { headers: DEFAULT_HEADERS, tags: { name: 'POST /api/auth/login' } },
  );

  const ok = check(res, {
    'status 200': (r) => r.status === 200,
    'có token':   (r) => !!r.json('data.access_token'),
  });

  successRate.add(ok);

  if (res.status === 200) {
      const token = res.json('data.access_token');
    http.post(
      `${BASE_URL}/api/auth/logout`,
      null,
      { headers: { ...DEFAULT_HEADERS, Authorization: `Bearer ${token}` } },
    );
  }

  sleep(0.2);
}
