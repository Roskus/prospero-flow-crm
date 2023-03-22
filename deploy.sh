git fetch
# Update branch
git pull origin main
# Install dependencies
composer install --no-dev --optimize-autoloader
# Run DB migration
php artisan migrate --force
# Generate updated api doc
php artisan l5-swagger:generate
# Fix storage permision also for logs
chmod 775 storage/ -R
# Get git revision commit
GIT_HASH=git rev-parse HEAD
# Get APP_VERSION
VERSION=$(sed -n "s/.*APP_VERSION = '\(.*\)';/\1/p" version.php)
# Notify sentry new release version
sentry-cli releases -o roskus-fo new -p prospectflow $VERSION
# Associate commits with the release
sentry-cli releases set-commits --auto $GIT_HASH
