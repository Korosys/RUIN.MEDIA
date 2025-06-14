# RUIN.MEDIA - Making the internet worse, one file at a time.

RUIN.MEDIA is a Docker-based PHP application for reducing the quality of images and audio.

## Features
- Two levels of degradation!
- All processing happens clientside, no data is stored on the server.
- Process management via Supervisor
- based on alpine for a small footprint


## Quick Start
1. Docker Compose:
   ```
   ---
   services:
     ruin.media:
       image: korosys/ruin.media:latest
       container_name: "ruin.media"
       ports:
         - "8080:80"
       restart: unless-stopped
   ```
3. Access the application at: `http://localhost:8080`
