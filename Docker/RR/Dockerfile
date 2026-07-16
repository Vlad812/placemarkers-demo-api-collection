FROM ghcr.io/roadrunner-server/roadrunner:2025.1.0 AS roadrunner
FROM php:8.5-cli

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libonig-dev \
    libssl-dev \
    pkg-config \
    $PHPIZE_DEPS \
    && docker-php-ext-install sockets mbstring \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

WORKDIR /var/www/html

CMD ["rr", "serve", "-c", ".rr.dev.yaml"]
