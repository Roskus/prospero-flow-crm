git fetch
# Update branch
git pull origin main
# Install dependencies
composer install --no-dev --optimize-autoloader
# Run DB migration
php artisan migrate --force
# Generate updated api doc
php artisan l5-swagger:generate
# Fix storage permission also for logs
sudo chmod 775 storage/ -R
# Get APP_VERSION
VERSION=$(sed -n "s/.*APP_VERSION = '\(.*\)';/\1/p" version.php)
export SENTRY_ORG=roskus-fo
#debug|info
export SENTRY_LOG_LEVEL=debug
# Notify sentry new release version
sentry-cli releases new -p prospectflow $VERSION
# Associate commits with the release
sentry-cli releases set-commits --auto $VERSION
