#!/bin/bash

# Set noninteractive mode and timezone
export DEBIAN_FRONTEND=noninteractive
export TZ=Asia/Manila

# Update and upgrade packages
apt update
apt upgrade -y

# Install essential packages and tools
apt install -y build-essential nginx software-properties-common zip unzip php7.4 php7.4-fpm php7.4-curl php7.4-ldap php7.4-mysql php7.4-gd php7.4-xml php7.4-mbstring php7.4-zip php7.4-bcmath composer curl wget nano

# Add PHP repositories and install multiple PHP versions
add-apt-repository ppa:ondrej/php
apt-get install -y php8.0 php8.0-fpm php8.0-curl php8.0-ldap php8.0-mysql php8.0-gd php8.0-xml php8.0-mbstring php8.0-zip php8.0-bcmath
apt-get install -y php8.1 php8.1-fpm php8.1-curl php8.1-ldap php8.1-mysql php8.1-gd php8.1-xml php8.1-mbstring php8.1-zip php8.1-bcmath php8.1-imagick
apt-get install -y php8.2 php8.2-fpm php8.2-curl php8.2-ldap php8.2-mysql php8.2-gd php8.2-xml php8.2-mbstring php8.2-zip php8.2-bcmath

# Install Certbot and necessary PHP packages
apt-get install -y certbot python3-certbot-nginx php php-fpm php-curl php-ldap php-mysql php-gd php-xml php-mbstring php-zip php-bcmath php-imagick

# Configure ImageMagick policy (uncomment the line manually in /etc/ImageMagick-6/policy.xml)
# nano /etc/ImageMagick-6/policy.xml <<-- manual to comment <policy domain="coder" rights="none" pattern="PDF" />

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Restart Nginx
systemctl restart nginx.service

# Create and configure swap file
fallocate -l 4G /swapfile
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile
cp /etc/fstab /etc/fstab.bak
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab

# Configure system settings
echo 'vm.swappiness=10' >> /etc/sysctl.conf
echo 'fs.inotify.max_user_watches=524288' >> /etc/sysctl.conf
sysctl -p
cp bolt.so /usr/lib/php/20210902/