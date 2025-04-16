# Use the official PHP image from Docker Hub
FROM php:7.4-cli

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the current directory contents into the container at /var/www/html
COPY . /var/www/html

# Install PHP extensions needed for cURL
RUN apt-get update && apt-get install -y libcurl4-openssl-dev && docker-php-ext-install curl

# Expose the port that PHP will listen on
EXPOSE 80

# Start the PHP built-in server
CMD ["php", "-S", "0.0.0.0:80"]
