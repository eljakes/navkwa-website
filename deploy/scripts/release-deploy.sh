#!/usr/bin/env bash
set -euo pipefail

# Release-based Laravel deployment for Navkwa apps.
#
# Required environment:
#   APP_SLUG=navkwa-website
#   REPO_URL=git@github.com:eljakes/navkwa-website.git
#
# Optional environment:
#   BRANCH=main
#   BASE_ROOT=/var/www
#   KEEP_RELEASES=5
#   PHP_BIN=php
#   COMPOSER_BIN=composer
#   WEB_USER=www-data

APP_SLUG="${APP_SLUG:-navkwa-website}"
REPO_URL="${REPO_URL:-}"
BRANCH="${BRANCH:-main}"
BASE_ROOT="${BASE_ROOT:-/var/www}"
KEEP_RELEASES="${KEEP_RELEASES:-5}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
WEB_USER="${WEB_USER:-www-data}"

if [[ -z "$REPO_URL" ]]; then
    echo "REPO_URL is required, for example git@github.com:eljakes/navkwa-website.git" >&2
    exit 1
fi

APP_ROOT="$BASE_ROOT/$APP_SLUG"
RELEASES_DIR="$APP_ROOT/releases"
SHARED_DIR="$APP_ROOT/shared"
RELEASE_ID="$(date +%Y%m%d_%H%M%S)"
RELEASE_DIR="$RELEASES_DIR/$RELEASE_ID"

run_privileged() {
    if [[ "$(id -u)" -eq 0 ]]; then
        "$@"
    else
        sudo "$@"
    fi
}

echo "Deploying $APP_SLUG release $RELEASE_ID from $BRANCH"

mkdir -p "$RELEASES_DIR" "$SHARED_DIR/storage" "$SHARED_DIR/logs"

if [[ ! -f "$SHARED_DIR/.env" ]]; then
    echo "Missing $SHARED_DIR/.env. Create it from .env.production.example before deploying." >&2
    exit 1
fi

git clone --depth 1 --branch "$BRANCH" "$REPO_URL" "$RELEASE_DIR"

ln -sfn "$SHARED_DIR/.env" "$RELEASE_DIR/.env"
rm -rf "$RELEASE_DIR/storage"
ln -sfn "$SHARED_DIR/storage" "$RELEASE_DIR/storage"

cd "$RELEASE_DIR"

"$COMPOSER_BIN" install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction

if [[ -f package.json ]]; then
    npm ci
    npm run build
fi

"$PHP_BIN" artisan migrate --force
"$PHP_BIN" artisan storage:link
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache
"$PHP_BIN" artisan event:cache

run_privileged chown -R "$WEB_USER:$WEB_USER" "$SHARED_DIR/storage" "$SHARED_DIR/logs" "$RELEASE_DIR/bootstrap/cache"
chmod -R ug+rwX "$SHARED_DIR/storage" "$SHARED_DIR/logs" "$RELEASE_DIR/bootstrap/cache"

ln -sfn "$RELEASE_DIR" "$APP_ROOT/current.tmp"
mv -Tf "$APP_ROOT/current.tmp" "$APP_ROOT/current"

"$PHP_BIN" artisan queue:restart || true

if command -v supervisorctl >/dev/null 2>&1; then
    run_privileged supervisorctl restart "$APP_SLUG-worker:*" || true
fi

find "$RELEASES_DIR" -mindepth 1 -maxdepth 1 -type d \
    | sort -r \
    | tail -n +"$((KEEP_RELEASES + 1))" \
    | xargs -r rm -rf

echo "Deployment complete: $APP_ROOT/current -> $RELEASE_DIR"
