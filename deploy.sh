#!/usr/bin/env bash
# Stop on first error so a failed step never leaves a half-deployed release
set -euo pipefail

if [ "$(id -u)" -eq 0 ]; then
    echo "Do not run deploy.sh as root, run it as the user that owns the code (deploy, in the www-data group)." >&2
    exit 1
fi

WEB_GROUP=www-data
WRITABLE_PATHS=(storage bootstrap/cache)

# Interactive runs may ask for the sudo password; non-interactive runs (CI/CD)
# fail fast instead of hanging when passwordless sudo is not configured.
if [ -t 0 ]; then
    SUDO=(sudo)
else
    SUDO=(sudo -n)
fi

# Make storage and cache writable by the web server group. Only touches entries that
# are not already correct, so on a healthy server it changes nothing and needs no sudo.
# setgid keeps the web group on files created later by any user.
fix_writable_permissions() {
    find "$@" ! -group "$WEB_GROUP" -exec chgrp "$WEB_GROUP" {} + &&
    find "$@" -type d ! -perm -2770 -exec chmod ug+rwx,g+s {} + &&
    find "$@" -type f ! -perm -0660 -exec chmod ug+rw {} +
}

# Copy a binary to /usr/local/bin only when it changed (prevents wkhtmltopdf error 126)
install_binary() {
    local source=$1
    local target="/usr/local/bin/$(basename "$source")"

    if cmp -s "$source" "$target" && [ -x "$target" ]; then
        return 0
    fi

    "${SUDO[@]}" install -m 755 "$source" "$target"
}

# Update branch. The GitHub Actions deploy updates the code itself, with a temporary
# token and to the exact commit that passed the tests, and sets SKIP_GIT_PULL=1.
if [ "${SKIP_GIT_PULL:-0}" != "1" ]; then
    git fetch
    git pull origin main
fi

# Install dependencies as the project owner (never as root)
composer install --no-dev --optimize-autoloader --no-interaction

# Run DB migration
php artisan migrate --force

# Sync roles and permissions (idempotent — safe to run on every deploy).
# Roles first: PermissionSeeder assigns permissions to roles and fails if a role is missing.
php artisan db:seed --class=RoleSeeder --force
# Modules: upserted by unique name, so permissions keep pointing to the same module ids
php artisan db:seed --class=ModuleSeeder --force
php artisan db:seed --class=PermissionSeeder --force
# Mobile carriers of each company country: only adds the missing ones, deleted carriers are not recreated
php artisan db:seed --class=CarrierSeeder --force
# Bank catalogue: upserted by a BIC-derived UUID, so existing bank accounts keep their bank
php artisan db:seed --class=BankSeeder --force

# Generate updated api doc
php artisan l5-swagger:generate

# Files created by PHP-FPM belong to www-data and can only be chmod-ed by their owner. By hand
# they are fixed with sudo; the CI deploy only warns, so the deploy user needs no root shell
# (the setgid directories and 0664 logs already give the www-data group write access).
if ! fix_writable_permissions "${WRITABLE_PATHS[@]}" 2>/dev/null; then
    if [ -t 0 ]; then
        echo "Some files in ${WRITABLE_PATHS[*]} belong to another user, fixing them with sudo..."
        sudo bash -c "WEB_GROUP=$WEB_GROUP; $(declare -f fix_writable_permissions); fix_writable_permissions ${WRITABLE_PATHS[*]}"
    else
        echo "Warning: some files in ${WRITABLE_PATHS[*]} belong to another user and keep their permissions." >&2
    fi
fi

# Compiled views must be owned by the web server user: Blade touch()es them with
# an explicit mtime, which only the file owner is allowed to do. Clear them so
# www-data recompiles them on the next request.
php artisan view:clear

# Queue workers (MailSender is queued) keep the old code in memory: ask them to restart
# after their current job. Harmless when no worker is running.
php artisan queue:restart

install_binary vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64
install_binary vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64

# Get APP_VERSION
VERSION=$(sed -n "s/.*APP_VERSION = '\(.*\)';/\1/p" version.php)
export SENTRY_ORG=roskus-fo
#debug|info
export SENTRY_LOG_LEVEL=debug
# Notify sentry new release version (skipped if auth token not configured)
sentry-cli releases new -p prospectflow "$VERSION" || true
# Associate commits with the release
sentry-cli releases set-commits --auto "$VERSION" || true
