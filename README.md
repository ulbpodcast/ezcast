# [EZcast - EZadmin, EZmanager & EZplayer](http://ezcast.ulb.ac.be)

## Overview

This package contains three web interfaces that are gathered under the **EZcast** project.

* **EZadmin** : The web interface for administration. It allows an administrator to create users, albums and handle the recorders.
* **EZmanager** : The web interface for videos management. It allows a user to handle his own albums and videos, submit videos and distribute them.
* **EZplayer** : The web interface for videos watching. It is a rich video player allowing the user to annotate his videos and interact with other users. 

All three applications are aimed to be installed on the same server. They interact with each other and, for that reason, need to share some files.

## How does it work ? 

EZmanager is the central part of the EZcast project. It is closely related to both EZadmin and EZplayer.
First, an administrator creates courses and users in EZadmin. Those users can then connect to EZmanager and creates albums (folders that contain videos) following the courses they have.
When an album has been created, the user can put videos in it, either by manual submission or by automated recording using EZrecorder.
Finally, the videos contained in albums can be published to a specific audience via EZplayer.

## Compatibility

EZcast requires a UNIX/LINUX architecture to run. It will NOT work on Windows.

## Prerequisites

Before installing EZcast, make sure the following commands / programs / libraries are correctly installed on your server:

* Apache 
* mySQL
* PHP5 
* php5_simplexml library
* php5_curl library
* [php5_ldap]
* php5_apc
* php5_mysql
* SSH
* AT 
* RSYNC

During the installation of EZcast, you will be requested to enter the path to the web space (DocumentRoot) and the Apache username. Make sure you know that information before you start the installation.

## Installation

Here is a quick installation guide. Refer to our website [EZcast](http://ezcast.ulb.ac.be) for detailed information.

1. Download the latest available version of EZcast from our Git repository
2. Create a MySQL database for EZcast on your server (grant all privileges on the database).
3. Put the EZcast directory wherever you want on your server (we recommend to place it under « /usr/local/ezcast») 
4. Edit the php.ini file to increase max file size upload.
5. Launch the « install.sh » script as root from the EZcast directory and follow the instructions.
6. Open the EZadmin page in your web browser for the configuration of EZcast and all of its components. This is a one-time operation
`i.e :	 http://my.server.address/ezadmin`

