#!/bin/bash

cd /var/www/magento2-release

# replace env.php
rsync -OvzrpLt ./build/env.php ./build/release-candidate/app/etc/

# build Magento 2
rm -rf ./build/release-candidate/pub/static
php ./build/release-candidate/bin/magento setup:static-content:deploy
php ./build/release-candidate/bin/magento setup:di:compile