#!/bin/bash

# might have to unmount NFS for media
if [ -d /var/www/magento2-release/release/pub/media ]; then
    umount -f -l /var/www/magento2-release/release/pub/media
fi