# Stage 1: Build front-end
FROM node:18-alpine

RUN apk update && \
	corepack enable && corepack prepare pnpm@latest --activate 

COPY ./client/ /var/www/client/

WORKDIR /var/www/client/
RUN pnpm install
RUN pnpm run build


# Stage 2: Build back-end
FROM php:8-fpm-alpine

RUN apk update && \
	apk add --no-cache linux-headers supervisor nginx dos2unix curl zlib-dev libzip-dev libjpeg-turbo-dev libpng-dev libpq-dev freetype-dev icu-dev oniguruma-dev && \
	docker-php-ext-install mbstring sockets intl zip gd pgsql pdo pdo_pgsql && \
	docker-php-ext-enable sockets && \
	curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER=1

ENV APP_NAME="Airport Booking" \
	APP_ENV=production \
	APP_DEBUG=false \
	DB_HOST=postgres

COPY ./server/ /var/www/server/
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY --from=0 /var/www/client/dist/ /var/www/client/

WORKDIR /var/www/server/
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts
RUN composer dump-autoload --optimize

RUN chown -R :www-data /var/www/server/ && \
	chown -R :www-data /var/www/client/ && \
	chmod -R 755 /var/www/server/ && \
	chmod -R 755 /var/www/client/


# Stage 3: Run
EXPOSE 8080

CMD ["sh", "-c", "nginx && php-fpm"]
