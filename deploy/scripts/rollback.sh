#!/usr/bin/env bash
set -euo pipefail

# Roll back a release-based Navkwa Laravel deployment.
#
# Optional environment:
#   APP_SLUG=navkwa-website
#   BASE_ROOT=/var/www
#   TARGET_RELEASE=/var/www/navkwa-website/releases/20260814_001
#   PHP_BIN=php

APP_SLUG="${APP_SLUG:-navkwa-website}"
BASE_ROOT="${BASE_ROOT:-/var/www}"
PHP_BIN="${PHP_BIN:-php}"
APP_ROOT="$BASE_ROOT/$APP_SLUG"
RELEASES_DIR="$APP_ROOT/releases"

run_privileged() {
    if [[ "$(id -u)" -eq 0 ]]; then
        "$@"
    else
        sudo "$@"
    fi
}

if [[ -n "${TARGET_RELEASE:-}" ]]; then
    ROLLBACK_RELEASE="$TARGET_RELEASE"
else
    ROLLBACK_RELEASE="$(find "$RELEASES_DIR" -mindepth 1 -maxdepth 1 -type d | sort -r | sed -n '2p')"
fi

if [[ -z "$ROLLBACK_RELEASE" || ! -d "$ROLLBACK_RELEASE" ]]; then
    echo "No rollback release found. Set TARGET_RELEASE to a valid release directory." >&2
    exit 1
fi

ln -sfn "$ROLLBACK_RELEASE" "$APP_ROOT/current.tmp"
mv -Tf "$APP_ROOT/current.tmp" "$APP_ROOT/current"

cd "$APP_ROOT/current"
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache
"$PHP_BIN" artisan queue:restart || true

if command -v supervisorctl >/dev/null 2>&1; then
    run_privileged supervisorctl restart "$APP_SLUG-worker:*" || true
fi

echo "Rollback complete: $APP_ROOT/current -> $ROLLBACK_RELEASE"
