#!/bin/sh
set -e

# Restore license from persistent volume if exists
LICENSE_VOL="/var/www/html/site/license/.license"
LICENSE_DST="/var/www/html/site/config/.license"
if [ -f "$LICENSE_VOL" ]; then
  cp "$LICENSE_VOL" "$LICENSE_DST"
  chown www-data:www-data "$LICENSE_DST"
fi

# Clear Kirby cache to ensure fresh config from environment variables
# This is necessary because the cache directory persists across deployments
# and may contain stale config (e.g., old license state)
rm -rf /var/www/html/site/cache/*

# Start supervisord
exec supervisord -c /etc/supervisord.conf
