FROM php:7.4-apache

RUN docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql

# --- HTTPS con certificato self-signed ---
# 1. Abilita il modulo SSL di Apache
# 2. Genera un certificato autofirmato valido 365 giorni
# 3. Abilita il VirtualHost SSL di default di Apache
RUN a2enmod ssl \
    && openssl req -x509 -nodes -days 365 \
       -newkey rsa:2048 \
       -keyout /etc/ssl/private/selfsigned.key \
       -out  /etc/ssl/certs/selfsigned.crt \
       -subj "/C=IT/ST=Lazio/L=Roma/O=Sapienza/CN=localhost" \
    && sed -i 's|/etc/ssl/certs/ssl-cert-snakeoil.pem|/etc/ssl/certs/selfsigned.crt|g'   /etc/apache2/sites-available/default-ssl.conf \
    && sed -i 's|/etc/ssl/private/ssl-cert-snakeoil.key|/etc/ssl/private/selfsigned.key|g' /etc/apache2/sites-available/default-ssl.conf \
    && a2ensite default-ssl

COPY . /var/www/html/
