#!/usr/bin/env bash
# =============================================================================
# k6/run.sh — Helper chạy load test suite
#
# Usage:
#   bash k6/run.sh smoke
#   bash k6/run.sh load
#   bash k6/run.sh stress
#   bash k6/run.sh spike
#   bash k6/run.sh all       ← chạy tuần tự: smoke → load → stress → spike
#
# Options (env):
#   BASE_URL=http://localhost:8000   (default)
#   OUT=html                          lưu báo cáo HTML vào k6/results/
# =============================================================================
set -euo pipefail

BASE_URL="${BASE_URL:-http://localhost:8000}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
RESULTS_DIR="${SCRIPT_DIR}/results"

mkdir -p "${RESULTS_DIR}"

run_test() {
  local name="$1"
  local script="${SCRIPT_DIR}/${name}.js"
  local timestamp
  timestamp=$(date +%Y%m%d_%H%M%S)
  local out_file="${RESULTS_DIR}/${name}_${timestamp}.html"

  echo ""
  echo "=========================================="
  echo "  k6: ${name^^} TEST"
  echo "  BASE_URL : ${BASE_URL}"
  echo "  Report   : ${out_file}"
  echo "=========================================="

  k6 run \
    --env BASE_URL="${BASE_URL}" \
    --out "json=${RESULTS_DIR}/${name}_${timestamp}.json" \
    "${script}"

  echo ""
  echo "✓ ${name^^} done — JSON log: ${RESULTS_DIR}/${name}_${timestamp}.json"
}

case "${1:-}" in
  smoke)  run_test smoke  ;;
  load)   run_test load   ;;
  stress) run_test stress ;;
  spike)  run_test spike  ;;
  all)
    run_test smoke
    echo "Chờ 10s trước load test..."
    sleep 10
    run_test load
    echo "Chờ 30s trước stress test..."
    sleep 30
    run_test stress
    echo "Chờ 30s trước spike test..."
    sleep 30
    run_test spike
    echo ""
    echo "=========================================="
    echo "  Tất cả test hoàn thành!"
    echo "  Kết quả: ${RESULTS_DIR}/"
    echo "=========================================="
    ;;
  *)
    echo "Usage: bash k6/run.sh [smoke|load|stress|spike|all]"
    echo ""
    echo "  smoke   — Kiểm tra cơ bản, 1 VU, 30s"
    echo "  load    — RPS test, ramp 10→50 VUs, ~3 phút"
    echo "  stress  — Tìm breaking point, ramp đến 200 VUs, ~7 phút"
    echo "  spike   — Traffic burst, 500 VUs trong 10s, ~3 phút"
    echo "  all     — Chạy tuần tự tất cả"
    exit 1
    ;;
esac
