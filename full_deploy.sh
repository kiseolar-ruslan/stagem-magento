#!/bin/bash
echo "maintanance enabling"
php bin/magento maintenance:enable

echo "Removing pub/static/adminhtml..."
rm -rf pub/static/adminhtml/

echo "Removing pub/static/frontend..."
rm -rf pub/static/frontend/

echo "removing deployed_version"
rm pub/static/deployed_version.txt
 
echo "Removing var/cache/ var/generation/ var/page_cache/ var/view_preprocessed/"
rm -rf var/cache/ var/generation/ var/page_cache/ var/view_preprocessed/ generated/
 
echo "Setup:upgrade..."
php -d memory_limit=-1 bin/magento setup:upgrade

echo "di:compile..."
php -d memory_limit=-1 bin/magento setup:di:compile
 
echo "static-content:deploy with params..."
php -d memory_limit=-1 bin/magento setup:static-content:deploy -f en_US

echo "removing cache"
php -d memory_limit=-1 bin/magento c:f

echo "maintanance disabling"
php -d memory_limit=-1 bin/magento maintenance:disable

