#!/bin/bash 

SERVER=$1
PORT=$2
EZMANAGER_BASEDIR="$3"

ssh ezrenderer@$SERVER '/usr/local/sbin/ffmpeg -i udp://localhost:$PORT -threads 0 -s 720x576 -f hls -hls_time 3 -hls_list_size 0 -hls_wrap 5 -y /var/www/hls/video/demo.m3u8 </dev/null >/dev/null 2> /var/www/hls/video/ffmpeg.log & echo $! ' > $EZMANAGER_BASEDIR/var/pid & 
