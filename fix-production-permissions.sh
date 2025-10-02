#!/bin/bash

# Script to fix Laravel permissions on production server
# Run this script on your production server

echo "Fixing Laravel permissions on production server..."

# Set the project path
PROJECT_PATH="/var/www/kaiproject1"

# 1. Fix storage permissions
echo "1. Fixing storage permissions..."
sudo chown -R www-data:www-data $PROJECT_PATH/storage
sudo chown -R www-data:www-data $PROJECT_PATH/bootstrap/cache

# 2. Set proper permissions
echo "2. Setting proper permissions..."
sudo chmod -R 775 $PROJECT_PATH/storage
sudo chmod -R 775 $PROJECT_PATH/bootstrap/cache

# 3. Create log file if not exists
echo "3. Creating log file..."
sudo touch $PROJECT_PATH/storage/logs/laravel.log
sudo chown www-data:www-data $PROJECT_PATH/storage/logs/laravel.log
sudo chmod 664 $PROJECT_PATH/storage/logs/laravel.log

# 4. Fix GCS service account file permissions
echo "4. Fixing GCS service account file permissions..."
if [ -f "$PROJECT_PATH/storage/app/gcs-service-account.json" ]; then
    sudo chown www-data:www-data $PROJECT_PATH/storage/app/gcs-service-account.json
    sudo chmod 644 $PROJECT_PATH/storage/app/gcs-service-account.json
fi

# 5. Clear cache
echo "5. Clearing cache..."
sudo -u www-data php $PROJECT_PATH/artisan cache:clear
sudo -u www-data php $PROJECT_PATH/artisan config:clear
sudo -u www-data php $PROJECT_PATH/artisan view:clear

# 6. Optimize for production
echo "6. Optimizing for production..."
sudo -u www-data php $PROJECT_PATH/artisan config:cache
sudo -u www-data php $PROJECT_PATH/artisan route:cache
sudo -u www-data php $PROJECT_PATH/artisan view:cache

echo "Permission fix completed!"
echo "Please restart your web server (nginx/apache) if needed."
