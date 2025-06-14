FROM php:8.2-fpm-alpine

# Install FFmpeg, supervisor, and dependencies
RUN apk add --no-cache ffmpeg supervisor nginx
# Set PHP upload limits
RUN echo "upload_max_filesize = 50M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/uploads.ini

# Copy configurations
COPY docker/supervisor.conf /etc/supervisor/conf.d/supervisor.conf
COPY docker/nginx-main.conf /etc/nginx/nginx.conf
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY app/index.php /var/www/html/
COPY app/favicon.ico /var/www/html/
EXPOSE 80
# Start supervisor which will manage both nginx and our cleanup process
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisor.conf"]
