#!/bin/bash
set -e

cd /var/www; php artisan config:cache
php artisan storage:link

# Add cron job into cronfile
echo "* * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1" >> cronfile

# Install cron job
crontab cronfile

# Remove temporary file
rm cronfile

env >> /var/www/.env
php-fpm8.0 -D

# Start cron
cron

nohup php artisan queue:work --daemon &

nginx -g "daemon off;"
