# Stage 1: Build CSS with Tailwind
FROM node:20-alpine AS css-builder

WORKDIR /build

# Install pnpm
RUN corepack enable && corepack prepare pnpm@10.28.0 --activate

# Copy package files
COPY package.json pnpm-lock.yaml ./

# Install dependencies
RUN pnpm install --frozen-lockfile

# Copy source files for Tailwind
COPY src/ ./src/
COPY tailwind.config.js* ./
COPY site/templates/ ./site/templates/
COPY site/snippets/ ./site/snippets/

# Build minified CSS
RUN pnpm build


# Stage 2: PHP dependencies
FROM composer:2 AS php-builder

WORKDIR /build

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs


# Stage 3: Final image with PHP-FPM + Nginx
FROM php:8.3-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    aws-cli \
    # Required for GD extension (og-image plugin)
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    # Required for intl extension
    icu-dev \
    # Required for zip extension
    libzip-dev \
  && docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
  && docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    opcache \
    zip

# Create required directories
RUN mkdir -p \
    /var/www/html \
    /var/log/supervisor \
    /run/nginx

WORKDIR /var/www/html

# Copy PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/kirby.ini

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Copy backup script
COPY docker/backup-to-s3.sh /usr/local/bin/backup-to-s3
RUN chmod +x /usr/local/bin/backup-to-s3

# Copy Kirby core from composer
COPY --from=php-builder /build/kirby ./kirby
COPY --from=php-builder /build/vendor ./vendor

# Copy built CSS
COPY --from=css-builder /build/assets/css ./assets/css

# Copy application files
COPY index.php ./
COPY site ./site
COPY assets ./assets
COPY bin ./bin

# Create placeholder directories for volumes (will be overwritten by mounts)
RUN mkdir -p content media site/accounts site/cache site/sessions \
  && touch content/index.html media/index.html site/accounts/index.html \
         site/cache/index.html site/sessions/index.html

# Set ownership
RUN chown -R www-data:www-data /var/www/html

# Supervisord configuration
COPY <<EOF /etc/supervisord.conf
[supervisord]
nodaemon=true
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid
user=root

[program:php-fpm]
command=php-fpm -F
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=nginx -g 'daemon off;'
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
EOF

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisord.conf"]
