FROM php:5.6-fpm
RUN ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime
ADD sources.list /etc/apt/sources.list
RUN apt-get update && apt-get install -y \
        vim \
        libmemcached-dev \
        libmcrypt-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng12-dev \
        cron \
        && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
        && docker-php-ext-install mysqli \
        && docker-php-ext-install pdo_mysql \
        && docker-php-ext-install zip \
        && docker-php-ext-install gd \
        && docker-php-ext-install mbstring \
        && docker-php-ext-install mcrypt \
        && docker-php-ext-install sockets

RUN pecl install binpack && pecl install redis-2.2.8 && pecl install sundown-beta
COPY .bashrc /root/
