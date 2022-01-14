FROM php:8.1-apache

RUN apt-get autoclean

RUN apt-get update

# 1. development packages
RUN apt-get install -y \
    git \
    zip \
    zlib1g-dev  \
    unzip \
    libzip-dev \
    libfreetype6-dev \
	supervisor

RUN \
  apt-get -y autoremove && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y libc-client-dev libkrb5-dev && rm -r /var/lib/apt/lists/*

# 3. mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
RUN a2enmod rewrite headers

# 4. start with base php config, then add extensions
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-install \
    pdo_mysql \
    zip

RUN docker-php-ext-configure zip && \
    docker-php-ext-install zip

# 6. Configure needed apache modules and disable default site
RUN a2dismod   mpm_event  cgi # mpm_worker enabled.
RUN a2enmod		\
  access_compat		\
  actions		\
  alias			\
  auth_basic		\
  authn_core		\
  authn_file		\
  authz_core		\
  authz_groupfile	\
  authz_host 		\
  authz_user		\
  autoindex		\
  dir			\
  env 			\
  expires 		\
  filter 		\
  headers		\
  mime 			\
  negotiation 		\
  mpm_prefork 		\
  reqtimeout 		\
  rewrite 		\
  setenvif 		\
  status 		\
  ssl

# 7. composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


#9. custom files
COPY ./docker_configs/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./docker_configs/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

#10. Apache config
COPY ./docker_configs/000-default.conf /etc/apache2/sites-available/000-default.conf

#11. set script with entrypoint
COPY project_setup.sh /usr/local/bin/dockerInit1

RUN chmod +x /usr/local/bin/dockerInit1

ENTRYPOINT service apache2 start \
  && service supervisor start \
  && dockerInit1 \
  && /bin/bash
