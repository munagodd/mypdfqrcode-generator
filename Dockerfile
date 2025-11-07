FROM php:8.2-apache

WORKDIR /var/www/html

# Copy all files
COPY . .

# Install required PHP extensions
RUN apt-get update && apt-get install -y libpng-dev \
    && docker-php-ext-install gd

# Enable mod_rewrite (optional)
RUN a2enmod rewrite

# Change Apache port to Render’s expected port
RUN sed -i 's/80/10000/' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

CMD ["apache2-foreground"]