FROM gymbeamdevops/conveyor-fpm-base:latest
ARG APP_MODE

RUN if [ "$APP_MODE" = "dev" ]; then \
    # install xdebug
    echo "Building in development mode" \
    && apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "alias php-xdebug='XDEBUG_CONFIG=idekey= php -d xdebug.mode=debug'" >> /etc/profile; \
  else \
    echo "Building in production mode"; \
  fi

# Copy crontab file to the cron.d directory
COPY crontab /etc/cron.d/crontab

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/crontab

# Apply cron job
RUN crontab /etc/cron.d/crontab

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

COPY start.sh /start.sh
CMD ["/start.sh"]