#
# This is an automatically generated Dockerfile, do not change this!
#
FROM php:7.4-fpm

RUN mkdir -p /usr/share/nginx/www/spenden.wikimedia.de/current/var/cache \
    && mkdir -p /usr/share/nginx/www/spenden.wikimedia.de/current/var/doctrine_proxies \
    && mkdir -p /usr/share/nginx/www/spenden.wikimedia.de/current/var/log \
    && chown -R www-data:www-data /usr/share/nginx/www/spenden.wikimedia.de/current/var

# Installing php extensions
# The following php extensions are assumed to be present: xml curl mbstring pdo_sqlite
RUN apt-get update \
	# regex library needed for PHP 7.4
	&& apt-get install libonig-dev \
    # for intl
    && apt-get install -y libicu-dev \
    # for konto_check
	&& apt-get install -y unzip libz-dev \
    && docker-php-ext-install -j$(nproc) intl pdo_mysql

RUN docker-php-source extract \
    && cd /tmp \
    && curl -Ls -o konto_check-6.13.zip https://sourceforge.net/projects/kontocheck/files/konto_check-de/6.13/konto_check-6.13.zip/download  \
    && unzip konto_check-*.zip \
    && cd konto_check-6.13 \
    && cp blz.lut2f /etc/blz.lut \
    && unzip php.zip \
    && cd php \
    && docker-php-ext-configure /tmp/konto_check-6.13/php \
    && docker-php-ext-install /tmp/konto_check-6.13/php \
    && docker-php-source delete \
    && rm -rf /tmp/konto_check-*

RUN apt-get install -y msmtp \
    && printf "account default\nhost mailhog\nport 1025\nauto_from on\n" > /etc/msmtprc

COPY ./mailhog.ini /usr/local/etc/php/conf.d/mailhog.ini
RUN pecl install xdebug-3.0.2 \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini
ARG BUILD_DATE
ARG VCS_REF
ARG BUILD_VERSION

LABEL maintainer="fundraising-tech@wikimedia.de"
LABEL org.label-schema.schema-version="1.0"
LABEL org.label-schema.build-date=$BUILD_DATE
LABEL org.label-schema.name="wikimediade/fundraising-frontend"
LABEL org.label-schema.description="PHP runtime environment for WMDE Fundraising App"
LABEL org.label-schema.url="https://github.com/wmde/FundraisingFrontend"
LABEL org.label-schema.vcs-url="https://github.com/wmde/fundraising-frontend-docker"
LABEL org.label-schema.vcs-ref=$VCS_REF
LABEL org.label-schema.vendor="Wikimedia Deutschland e.V."
LABEL org.label-schema.version=$BUILD_VERSION