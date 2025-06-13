FROM lscr.io/linuxserver/nginx

# Install FFmpeg and dependencies
RUN apk add --no-cache ffmpeg
