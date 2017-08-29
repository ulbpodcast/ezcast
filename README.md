# [EZcast - EZadmin, EZmanager & EZplayer](http://ezcast.ulb.ac.be)

## Overview

This package contains three web interfaces that are gathered under the **EZcast** project.

* **EZadmin** : The web interface for administration. It allows an administrator to create users, albums and handle the recorders.
* **EZmanager** : The web interface for videos management. It allows a user to handle his own albums and videos, submit videos and distribute them. It requires EZrenderer to work.
* **EZplayer** : The web interface for watching the videos. It is a rich video player allowing the user to annotate his videos and interact with other users. 

All three applications are aimed to be installed on the same server. They interact with each other and, for that reason, need to share some files.

## How does it work ? 

EZmanager is the central part of the EZcast project. It is closely related to both EZadmin and EZplayer.
First, an administrator creates courses and users in EZadmin. Those users can then connect to EZmanager and create albums (folders containing videos) according to the authorisations granted in EZadmin.
When an album has been created, the user can store videos in it, either by manual submission (through EZmanager web interface) or by automated recording using EZrecorder.
Finally, the end users (eg: students) can access the videos via EZplayer.

## Compatibility

EZcast requires a UNIX/LINUX architecture to run. It will NOT work on Windows.

## Requirements

In order to use EZrecorder, you need to install / enable several components:

- Apache2 
- MySQL 5.x
- PHP5.6 ( or greater ) with following extensions activated:
  -curl
  -ldap
  -pdo_mysql
- ssh (unix command, usually installed by default)
- at (unix command, usually installed by default)
- rsync (unix command, usually installed by default)

During the installation of EZcast, you will be requested to enter the path to the web space (DocumentRoot) and the Apache username. Make sure you know that information before you start the installation.

## Installation

Read the INSTALL file for install instructions.
