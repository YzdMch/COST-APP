# ---- Stage 1: Build Tailwind CSS ----
FROM node:20-alpine AS tailwind-builder

WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci

COPY tailwind.config.js ./
COPY src/css/input.css ./src/css/input.css
COPY src/ ./src/

RUN mkdir -p dist/css && \
    npx tailwindcss -i ./src/css/input.css -o ./dist/css/style.css --minify

# ---- Stage 2: PHP + Apache ----
FROM php:8.2-apache

# Install PHP extensions untuk MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite alias

# Copy project ke /var/www/html/COST-APP
COPY . /var/www/html/COST-APP/

# Copy Tailwind CSS hasil build dari stage 1
COPY --from=tailwind-builder /build/dist/css/style.css /var/www/html/COST-APP/dist/css/style.css

# Copy Apache config
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html/COST-APP && \
    chmod -R 755 /var/www/html/COST-APP

# Buat folder uploads jika belum ada
RUN mkdir -p /var/www/html/COST-APP/public/uploads && \
    chown www-data:www-data /var/www/html/COST-APP/public/uploads

EXPOSE 80

CMD ["apache2-foreground"]
