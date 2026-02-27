#!/bin/bash
set -e

echo "==> Waiting for MySQL..."
until mysqladmin ping -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --silent 2>/dev/null; do
    sleep 2
done
echo "==> MySQL is ready."

if [ ! -d "/var/www/vendor" ]; then
    echo "==> Installing composer dependencies..."
    composer install --working-dir=/var/www --no-interaction
fi

mkdir -p /var/www/runtime /var/www/web/assets
chown -R www-data:www-data /var/www/runtime /var/www/web/assets

echo "==> Running migrations..."
php /var/www/yii migrate --interactive=0

echo "==> Starting PHP-FPM..."
exec "$@"
