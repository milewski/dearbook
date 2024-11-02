ARG ENVIRONMENT

FROM dunglas/frankenphp:1.2-php8.3-alpine AS base

ARG REDIS_VERSION=6.0.2

# Prepare redis extension
RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/$REDIS_VERSION.tar.gz \
    && mkdir -p /usr/src/php/ext/redis \
    && tar xfz /tmp/redis.tar.gz --directory /usr/src/php/ext/redis \
    && rm -r /tmp/redis.tar.gz

# Install Composer and enable all necessary dependencies for laravel to function
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache curl-dev git icu-dev libxml2-dev libpq-dev libzip-dev libpng-dev oniguruma-dev linux-headers \
    && docker-php-ext-install -j$(nproc) \
           bcmath curl intl mbstring pcntl pdo pdo_pgsql xml zip gd \
           redis/phpredis-$REDIS_VERSION

# Install a CRON alternative which is designed to work with containers!
RUN wget -q "https://github.com/aptible/supercronic/releases/download/v0.2.29/supercronic-linux-amd64" \
         -O /usr/bin/supercronic \
    && chmod +x /usr/bin/supercronic

# For security do never run any process inside the PHP container with ROOT
RUN addgroup --gid 1000 composer \
    && adduser --disabled-password --ingroup composer --uid 1000 composer

FROM base AS development

RUN mkdir -p /opt/phpstorm-coverage \
    && chown -R 1000:1000 /opt/phpstorm-coverage

# Copy the config files to the right places
RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/conf.d/php.ini-development

COPY ./config/php-config.development.ini /usr/local/etc/php/conf.d/php-config.development.ini

FROM base AS production

# Copy the config files to the right places
RUN mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/conf.d/php.ini-production

COPY ./config/php-config.production.ini /usr/local/etc/php/conf.d/php-config.production.ini

COPY . /srv

FROM ${ENVIRONMENT}

COPY ./config/Caddyfile /etc/caddy/Caddyfile

RUN chown -R composer:composer /srv
RUN mkdir -p $XDG_CONFIG_HOME && \
    chown -R composer:composer $XDG_CONFIG_HOME

USER composer

WORKDIR /srv
