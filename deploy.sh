git fetch
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
