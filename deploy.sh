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

# Copy and fix permissions for wkhtmltopdf Prevent error 126
cp vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64 /usr/local/bin/
cp vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64 /usr/local/bin/
chmod +x /usr/local/bin/wkhtmltoimage-amd64
chmod +x /usr/local/bin/wkhtmltopdf-amd64

# Get APP_VERSION
VERSION=$(sed -n "s/.*APP_VERSION = '\(.*\)';/\1/p" version.php)
export SENTRY_ORG=roskus-fo
#debug|info
export SENTRY_LOG_LEVEL=debug
# Notify sentry new release version
sentry-cli releases new -p prospectflow $VERSION
# Associate commits with the release
sentry-cli releases set-commits --auto $VERSION
