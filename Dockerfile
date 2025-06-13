FROM lscr.io/linuxserver/nginx

# Install FFmpeg, supervisor, and dependencies
RUN apk add --no-cache ffmpeg supervisor

# Copy supervisor configuration
COPY docker/supervisor.conf /etc/supervisor/conf.d/supervisor.conf
COPY app/* /config/www/cleanup_uploads.php
# Start supervisor which will manage both nginx and our cleanup process
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisor.conf"]
