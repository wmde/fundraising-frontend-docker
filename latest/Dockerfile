#
# This is an automatically generated Dockerfile, do not change this!
#
FROM php:7.3-fpm

RUN mkdir -p /usr/share/nginx/www/spenden.wikimedia.de/current/var/cache \
    && mkdir -p /usr/share/nginx/www/spenden.wikimedia.de/current/var/doctrine_proxies \
    && mkdir -p /usr/share/nginx/www/spenden.wikimedia.de/current/var/log \
    && chown -R www-data:www-data /usr/share/nginx/www/spenden.wikimedia.de/current/var

RUN apt-get update \
    # for intl
    && apt-get install -y libicu-dev \
    # for curl
    && apt-get install -y libcurl3-dev \
    # for xml
    && apt-get install -y libxml2-dev \
    # for konto_check
    && apt-get install -y unzip libz-dev \
    #&& docker-php-ext-install -j$(nproc) pdo_sqlite \
    && docker-php-ext-install -j$(nproc) intl curl xml pdo_mysql mbstring

RUN docker-php-source extract \
    && cd /tmp \
    && curl -Ls -o konto_check-6.08.zip https://sourceforge.net/projects/kontocheck/files/konto_check-de/6.08/konto_check-6.08.zip/download  \
    && unzip konto_check-*.zip \
    && cd konto_check-6.08 \
    && cp blz.lut2f /etc/blz.lut \
    && unzip php.zip \
    && cd php \
    && docker-php-ext-configure /tmp/konto_check-6.08/php \
    && docker-php-ext-install /tmp/konto_check-6.08/php \
    && docker-php-source delete \
    && rm -rf /tmp/konto_check-*

LABEL maintainer="fundraising-tech@wikimedia.de"

ARG BUILD_DATE
ARG VCS_REF
ARG BUILD_VERSION

# Labels.
LABEL org.label-schema.schema-version="1.0"
LABEL org.label-schema.build-date=$BUILD_DATE
LABEL org.label-schema.name="wikimediade/fundraising-frontend"
LABEL org.label-schema.description="PHP runtime environment for WMDE Fundraising App"
LABEL org.label-schema.url="https://github.com/wmde/FundraisingFrontend"
LABEL org.label-schema.vcs-url="https://github.com/wmde/fundraising-frontend-docker"
LABEL org.label-schema.vcs-ref=$VCS_REF
LABEL org.label-schema.vendor="Wikimedia Deutschland e.V."
LABEL org.label-schema.version=$BUILD_VERSION