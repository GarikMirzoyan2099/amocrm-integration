FROM php:8.2-fpm

ARG USER_UID=1000
ARG USER_GID=1000

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    htop \
    jpegoptim optipng pngquant gifsicle \
    nano \
    unzip \
    curl \
    libpq-dev \
    libonig-dev libzip-dev mc libxml2-dev libcurl4-openssl-dev pkg-config \
    git \
    procps

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pgsql pdo_pgsql iconv mbstring opcache zip exif pcntl xml curl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Add user for laravel application
RUN groupadd -g $USER_GID amo
RUN useradd -u $USER_UID -ms /bin/bash -g amo amo

RUN chown -R amo:amo /var/www/html/
# Copy existing application directory permissions
COPY --chown=amo:amo . /var/www

# Change current user to www
USER amo

CMD ["php-fpm"]