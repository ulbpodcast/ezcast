#!/bin/bash

#NAME        : deployement.sh
#DESCRIPTION : copy repos file to web path
#AUTHOR      : podcastdev

source $(dirname $0)/commons/localpaths

G='\033[32m\033[1m'
R='\033[31m\033[1m'
N='\033[0m'

clear;

webspace_directory=$apache_documentroot;
ezcast_basedir=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd );

echo ""
echo -e "${R}This script will delete all modifications do in $webspace_dir ${N}"
read -p "Are you sure you want to deploy the repos file to web folder ? [Y/n]: " choice;

if [ "$choice" != "n" ]; then

    # places web files in the webspace
    cp -rp -f $ezcast_basedir/ezadmin/htdocs $webspace_directory/ezadmin;
    cp -rp -f $ezcast_basedir/ezmanager/htdocs $webspace_directory/ezmanager;
    cp -rp -f $ezcast_basedir/ezplayer/htdocs $webspace_directory/ezplayer;

    cp -rp -f $ezcast_basedir/commons/htdocs $webspace_directory/ezadmin/commons;
    cp -rp -f $ezcast_basedir/commons/htdocs $webspace_directory/ezmanager/commons;
    cp -rp -f $ezcast_basedir/commons/htdocs $webspace_directory/ezplayer/commons;
    echo -e "${G}Files have been deployed${N}"

else
    echo "Goodbye";
    exit;
fi;


