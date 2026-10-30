FROM php:8.5.12-cli-alpine3.24

WORKDIR /var/www/html

COPY --chown=www-data:www-data . /var/www/html

USER www-data

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/html/public"]
