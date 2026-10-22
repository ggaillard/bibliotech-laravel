# Conteneur « app » : Apache + PHP 8.4 + Composer (repris de LaravelBase)
FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends git zip unzip default-mysql-client \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Apache sert le dossier public/ du projet Laravel
RUN sed -i 's#/var/www/html#/workspace/projetLaravel/public#g' /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /workspace/projetLaravel/public>\n    AllowOverride All\n    Require all granted\n</Directory>\nServerName localhost\n' \
       > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Droits : Apache (www-data) doit pouvoir écrire dans storage/ et bootstrap/cache/.
# Le script attend que le projet soit présent, puis corrige les droits.
# Le bit setgid fait hériter le groupe www-data aux fichiers créés ensuite,
# et umask 0002 (terminal root) les laisse modifiables par ce groupe :
# un « php artisan … » lancé au terminal ne bloque plus Apache.
RUN printf '#!/bin/bash\n\
while [ ! -f /workspace/projetLaravel/artisan ] || [ ! -d /workspace/projetLaravel/storage/framework/views ]; do sleep 1; done\n\
cd /workspace/projetLaravel\n\
chown -R www-data:www-data storage bootstrap/cache\n\
chmod -R ug+rwX storage bootstrap/cache\n\
find storage bootstrap/cache -type d -exec chmod g+s {} +\n' > /usr/local/bin/laravel-permissions \
    && chmod +x /usr/local/bin/laravel-permissions \
    && echo 'umask 0002' >> /root/.bashrc

WORKDIR /workspace/projetLaravel

CMD ["sh", "-c", "/usr/local/bin/laravel-permissions & exec apache2-foreground"]
