#!/usr/bin/env bash
# Stop on first error so a failed step never leaves a half-deployed release
set -euo pipefail

if [ "$(id -u)" -eq 0 ]; then
    echo "Do not run deploy.sh as root, run it as the project owner (e.g. ubuntu)." >&2
    exit 1
fi

git fetch
# Update branch
git pull origin main

# Install dependencies as the project owner (never as root)
composer install --no-dev --optimize-autoloader --no-interaction

# Run DB migration
php artisan migrate --force

# Sync permissions (idempotent — safe to run on every deploy)
php artisan db:seed --class=PermissionSeeder --force

# Generate updated api doc
php artisan l5-swagger:generate

# Web server (www-data group) must be able to write storage and cache, also for logs
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache

# Copy and fix permissions for wkhtmltopdf Prevent error 126
sudo cp vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64 /usr/local/bin/
sudo cp vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64 /usr/local/bin/
sudo chmod +x /usr/local/bin/wkhtmltoimage-amd64
sudo chmod +x /usr/local/bin/wkhtmltopdf-amd64

# Get APP_VERSION
VERSION=$(sed -n "s/.*APP_VERSION = '\(.*\)';/\1/p" version.php)
export SENTRY_ORG=roskus-fo
#debug|info
export SENTRY_LOG_LEVEL=debug
# Notify sentry new release version (skipped if auth token not configured)
sentry-cli releases new -p prospectflow $VERSION || true
# Associate commits with the release
sentry-cli releases set-commits --auto $VERSION || true
