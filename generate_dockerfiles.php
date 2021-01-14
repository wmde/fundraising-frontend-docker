#!/usr/bin/env php
<?php

$PHP_VERSION = "7.4";
$XDEBUG_VERSION = "3.0.2";
$KONTOCHECK_VERSION = "6.13";
$COMPOSER_VERSION = "2.0.8";
$PHPSTAN_VERSION = "^0.12";

$headerTemplate = <<<HD
#
# This is an automatically generated Dockerfile, do not change this!
#
HD;

$baseTemplate = <<<BASE
FROM php:{$PHP_VERSION}-fpm

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
    && curl -Ls -o konto_check-$KONTOCHECK_VERSION.zip https://sourceforge.net/projects/kontocheck/files/konto_check-de/$KONTOCHECK_VERSION/konto_check-$KONTOCHECK_VERSION.zip/download  \
    && unzip konto_check-*.zip \
    && cd konto_check-$KONTOCHECK_VERSION \
    && cp blz.lut2f /etc/blz.lut \
    && unzip php.zip \
    && cd php \
    && docker-php-ext-configure /tmp/konto_check-$KONTOCHECK_VERSION/php \
    && docker-php-ext-install /tmp/konto_check-$KONTOCHECK_VERSION/php \
    && docker-php-source delete \
    && rm -rf /tmp/konto_check-*

BASE;

$labelTemplate = <<<'LBL'
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
LBL;

$mailTemplate = <<<MAIL
RUN apt-get install -y msmtp \
    && printf "account default\\nhost mailhog\\nport 1025\\nauto_from on\\n" > /etc/msmtprc

COPY ./mailhog.ini /usr/local/etc/php/conf.d/mailhog.ini
MAIL;

$xdebugTemplate = <<<XDEBUG
RUN pecl install xdebug-$XDEBUG_VERSION \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini
XDEBUG;

$composerTemplate = <<<COMPOSER
RUN apt-get install -y git subversion mercurial bash patch make zip libzip-dev \
    && docker-php-ext-install -j$(nproc) zip

COPY --from=composer:$COMPOSER_VERSION /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp

WORKDIR /app

CMD ["composer"]
COMPOSER;

$stanTemplate = <<<STAN
RUN composer global require phpstan/phpstan $PHPSTAN_VERSION
ENTRYPOINT [ "php", "-d", "memory_limit=-1", "/tmp/vendor/bin/phpstan" ]
STAN;

$dockerfiles = [
  'latest' =>   [ $headerTemplate, $baseTemplate, $labelTemplate ],
  'dev' =>      [ $headerTemplate, $baseTemplate, $mailTemplate, $labelTemplate ],
  'xdebug' =>   [ $headerTemplate, $baseTemplate, $mailTemplate, $xdebugTemplate, $labelTemplate ],
  'composer' => [ $headerTemplate, $baseTemplate, $composerTemplate, $labelTemplate ],
  'stan' =>     [ $headerTemplate, $baseTemplate, $composerTemplate, $stanTemplate, $labelTemplate ],
];

foreach ( $dockerfiles as $path => $templates ) {
  if ( !file_exists( $path ) ) {
    mkdir( $path, 0777, true );
  }
  file_put_contents( "$path/Dockerfile", implode( "\n", $templates ) );
  if ( in_array( $mailTemplate, $templates ) ) {
    copy( 'mailhog.ini', "$path/mailhog.ini" );
  }
}
