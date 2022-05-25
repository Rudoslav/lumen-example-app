FROM gymbeamdevops/conveyor-fpm-base:latest
ARG APP_MODE

RUN if [ "$APP_MODE" = "dev" ]; then \
    # install xdebug
    echo "Building in development mode" \
    && apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug; \
  else \
    echo "Building in production mode"; \
  fi

COPY start.sh /start.sh
CMD ["/start.sh"]