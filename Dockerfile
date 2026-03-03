# Use the official PHP image with Apache
FROM php:8.2-apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy all application files into the container
COPY . /var/www/html

# Enable Apache's rewrite module (often needed)
RUN a2enmod rewrite

# Expose port 80 for the web server
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
