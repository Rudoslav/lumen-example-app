#!/bin/sh

cp /etc/group /tmp
sed -i 's/www-data:x:82/www-data:x:1000/' /tmp/group
cat /tmp/group >/etc/group

cp /etc/passwd /tmp
sed -i 's/www-data:x:82:82/www-data:x:1000:1000/' /tmp/passwd
cat /tmp/passwd >/etc/passwd

# start cron
/usr/sbin/crond

php-fpm -F