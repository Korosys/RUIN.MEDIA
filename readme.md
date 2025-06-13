# RUIN.MEDIA - Making the internet worse, one file at a time.

RUIN.MEDIA is a Docker-based PHP application for reducing the quality of images and audio with automated cleanup functionality.

## Features
- Two levels of degradation!
- Scheduled cleanup of old uploads
- Process management via Supervisor
- based on alpine for a small footprint


## Quick Start
1. Docker Compose:
   ```docker-compose up -d
   ```
3. Access the application at: `http://localhost:8080`


## Configuration
Customize settings by editing:
- Upload directory: `docker/nginx.conf` (line 15)
- Cleanup schedule: `docker/supervisor.conf` (cron entry)


## Cleanup Process
The `cleanup_uploads.php` script runs every minute to remove files older than 5 minutes. To modify:
