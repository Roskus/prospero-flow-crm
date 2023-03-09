git fetch
# Update branch
git pull origin main
# Install dependencies
composer install --no-dev --optimize-autoloader
# Run DB migration
php artisan migrate --force
# Generate updated api doc
php artisan l5-swagger:generate
# Get APP_VERSION
version=$(sed -n "s/.*APP_VERSION = '\(.*\)';/\1/p" version.php)
# Notify sentry new release version
sentry-cli releases -o roskus-fo new -p prospectflow $version
