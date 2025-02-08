FROM php:8.2-fpm

# Install system dependencies, including packages required by Chromium
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    pkg-config \
    wget \
    gnupg2 \
    libnss3 \
    libxss1 \
    libappindicator3-1 \
    fonts-liberation \
    xdg-utils \
    --no-install-recommends

# Install Chromium and ChromiumDriver (suitable for ARM architectures)
RUN apt-get update && apt-get install -y chromium chromium-driver && \
    # Create a symlink so that the driver is on the PATH
    ln -sf /usr/lib/chromium-browser/chromedriver /usr/local/bin/chromedriver && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure zip && \
    docker-php-ext-install pdo_pgsql intl opcache zip bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Verify installations: Composer, Chromium, and ChromiumDriver
RUN composer --version && chromium --version && chromedriver --version

WORKDIR /var/www/html
