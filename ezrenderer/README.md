# [EZrenderer](http://ezcast.ulb.ac.be)

## Overview

**EZrenderer** is part of the EZcast product. It is a program called by **EZmanager** to process videos.
The processing consists in adding a jingle and titling and re-encoding the movie in various resolution, using H264 video codec and AAC audio codec.

## How does it work ? 

EZmanager sends the videos to be processed to EZrenderer and executes a cli script through SSH. EZrenderer then proceeds to the rendering. It re-encodes the video using FFMPEG.
At the end of the processing, EZmanager fetches the rendered videos, using RSYNC, and places them in the repository.

## Compatibility

EZrecorder requires a UNIX/LINUX architecture to run. It will NOT work on Windows.

## Prerequisites

Before installing EZrenderer, make sure the following commands / programs / libraries are correctly installed on your server:

* EZcast (see our EZcast package on Git)
* Apache 
* PHP5 
* php5_simplexml library
* php5_gd library (with freetype enabled)
* SSH
* FFPROBE
* FFMPEG (with libx264 & libfdk_aac codecs) 

## Installation

Here is a quick installation guide. Refer to our website [EZcast](http://ezcast.ulb.ac.be) for detailed information.

1. Download the latest available version of EZrenderer from our Git repository
`git clone https://github.com/ulbpodcast/ezcast.git`
2. Put the 'ezrenderer' directory wherever you want on your server (we recommend to place it under "~/ezrenderer")
3. Launch the "install.sh" script as root from the 'ezrenderer' directory and follow the instructions on the screen
4. Configure the SSH link between EZmanager (EZcast server) and EZrenderer
5. Add the renderer to EZmanager and EZadmin

