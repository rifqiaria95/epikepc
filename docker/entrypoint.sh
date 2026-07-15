#!/bin/sh
set -e

cd /var/www

mkdir -p \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  storage/app/public \
  bootstrap/cache

# /storage URLs need this symlink. Bind mounts do not create it.
if [ ! -L public/storage ]; then
  rm -rf public/storage
  php artisan storage:link --force
fi

# Host volume often starts empty (only dirs). Fill seed images from compro assets once.
if [ ! -f storage/app/public/seed/gallery/image66.png ]; then
  echo "Seed media missing on volume — syncing from public/frontend/img/compro..."
  php artisan epik:sync-seed-media || true
fi

if [ -f .env ]; then
  if grep -q '^FILESYSTEM_DISK=' .env; then
    sed -i 's|^FILESYSTEM_DISK=.*|FILESYSTEM_DISK=public|' .env
  else
    echo 'FILESYSTEM_DISK=public' >> .env
  fi
fi

exec "$@"
