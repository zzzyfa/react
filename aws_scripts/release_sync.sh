#!/bin/bash

cd /var/www/magento2-release

RELEASE_DIR="/var/www/magento2-release/release"

# enable maintenance mode only if release directory is not empty
[ "$(ls -A $RELEASE_DIR)" ] && php ./release/bin/magento maintenance:enable
# sync release-candidate with release
rsync -OvzrgpL --delete --checksum --exclude-from='./build/.exclude-files-release' './build/release-candidate/' './release/'
# upgrade database without removing pre-generated files
php ./build/release-candidate/bin/magento setup:upgrade --keep-generated
# disable maintenance mode
php ./release/bin/magento maintenance:disable