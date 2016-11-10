# How to install EZrenderer ?

## Requirements

In order to use EZrecorder, you need to install / enable several components:

- PHP5
 - LIB SIMPLEXML for PHP5
 - LIB GD for PHP5 (with freetype enabled)
- SSH
- AT
- FFPROBE
- FFMPEG (with libx264 & libfdk_aac codecs) 

## Detailed installation 

1. Download the source code

Open your terminal and call Git in command line to download the source code of EZrenderer

```
git clone git clone https://github.com/ulbpodcast/ezcast.git
``` 

2. Move the 'ezrenderer' directory

Now you have to decide where you want to install EZrenderer. We recommend you to install it under '~/ezrenderer' directory.

3. Execute the 'install.sh' script for installing EZrenderer

Go in the 'ezrenderer' folder. Make sure the file 'install.sh' can be executed. 
Launch the 'install.sh' script as root and follow the instructions on the screen.

4. Configure the SSH link 

You are now going to configure the SSH link between EZmanager and EZrenderer. For this part of the installation, you will need an access to EZcast server.

On the remote EZcast server, copy the public SSH key.

On EZrenderer, generate a SSH key and add the public key from EZmanager in the authorized keys of EZrenderer

```
cd
# generate a key pair
ssh-keygen –t dsa 
cd .ssh
vi authorized_keys # may be authorized_keys2 
# paste the EZmanager public key in authorized_keys
```

5. Add the renderer to EZmanager and EZadmin

On the remote EZcast server, edit the ‘renderers.inc’ file in ezmanager and ezadmin folders, according to your own configuration.

```
<?php
// Renderers.inc
// Configuration file

return array(
 array(
    'name' => 'name of the renderer',
    'host' => 'localhost',
    'client' => 'user name',
    'status' => 'activate',
    'downloading_dir' => '/path/to/downloading', 
// typically ~/ezrenderer/queues/downloading
    'downloaded_dir' => '/path/to/downloaded', 
// typically ~/ezrenderer/queues/downloaded
    'processed_dir' => '/path/to/processed', 
// typically ~/ezrenderer/queues/processed
    'statistics' => '/path/to/bin/cli_statistics_get.php', 
// typically ~/ezrenderer/bin/cli_statistics_get.php
    'php' => '/usr/bin/php',
    'launch' => '/path/to/bin/intro_title_movie.bash', 
// typically ~/ezrenderer/bin/intro_title_movie.bash
    'kill' => '/path/to/bin/cli_job_kill.php', 
// typically ~/ezrenderer/bin/cli_job_kill.php
  ), 
);
?>
```
