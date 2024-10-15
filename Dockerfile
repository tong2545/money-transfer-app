# ใช้ PHP 7.4 กับ Apache เป็นฐาน
FROM php:7.4-apache

# ติดตั้ง PHP Extensions ที่ต้องการ
RUN docker-php-ext-install mysqli

# กำหนด Document Root
COPY ./app /var/www/html

# กำหนดสิทธิ์ให้กับไดเรกทอรีเพื่อให้ Apache สามารถเข้าถึงได้
RUN chown -R www-data:www-data /var/www/html

# เปิดใช้งาน mod_rewrite สำหรับ Apache
RUN a2enmod rewrite

# กำหนด DocumentRoot ให้กับ Apache
RUN echo 'DocumentRoot /var/www/html' > /etc/apache2/sites-available/000-default.conf

# ทำให้ Apache ทำงานในโหมด foreground
CMD ["apache2-foreground"]
