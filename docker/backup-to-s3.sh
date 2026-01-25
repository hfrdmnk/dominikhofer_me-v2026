#!/bin/sh
set -e

# S3 Backup Script for Kirby CMS
# Syncs content and accounts to S3 bucket
#
# Required environment variables:
#   AWS_ACCESS_KEY_ID
#   AWS_SECRET_ACCESS_KEY
#   AWS_DEFAULT_REGION (e.g., eu-central-1)
#   S3_BUCKET (e.g., dominikhofer-backups)

if [ -z "$S3_BUCKET" ]; then
  echo "Error: S3_BUCKET environment variable not set"
  exit 1
fi

echo "Starting backup at $(date)"

# Sync content folder (critical - contains all page content and uploaded files)
echo "Syncing content folder..."
aws s3 sync /var/www/html/content "s3://${S3_BUCKET}/kirby-content" --delete --quiet

# Sync accounts folder (contains Panel user accounts)
echo "Syncing accounts folder..."
aws s3 sync /var/www/html/site/accounts "s3://${S3_BUCKET}/kirby-accounts" --delete --quiet

echo "Backup completed successfully at $(date)"
