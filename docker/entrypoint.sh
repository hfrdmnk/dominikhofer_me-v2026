#!/bin/sh
set -e

# Clear Kirby cache to ensure fresh config from environment variables
# This is necessary because the cache directory persists across deployments
# and may contain stale config (e.g., old license state)
rm -rf /var/www/html/site/cache/*

# Start supervisord
exec supervisord -c /etc/supervisord.conf
