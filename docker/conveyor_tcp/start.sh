#!/bin/sh

cp /etc/group /tmp
sed -i 's/www-data:x:82/www-data:x:1000/' /tmp/group
cat /tmp/group >/etc/group

cp /etc/passwd /tmp
sed -i 's/www-data:x:82:82/www-data:x:1000:1000/' /tmp/passwd
cat /tmp/passwd >/etc/passwd

su - www-data -s /bin/sh -c "cd /var/www && php artisan conveyor-tcp:run $CONVEYOR_TCP_INSIDE_PORT"