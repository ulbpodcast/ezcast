#!/bin/bash

#NAME 		: clean.sh
#DESCRIPTION	: remove folders and files created during ezCast installation
#AUTHOR		: podcastdev

# includes localpaths 
source $(dirname $0)/commons/localpaths

G='\033[32m\033[1m'
R='\033[31m\033[1m'
N='\033[0m'

clear;
echo "Welcome in this installation script !";
echo " ";
echo "This script is aimed to properly uninstall EZcast:";
echo " ";
echo "*******************************************************************";
echo "*                                                                 *";
echo "*                        C L E A N E R                            *";
echo "*                                                                 *";
echo "*******************************************************************";
echo -e "${R}/!\WARNING : This script will delete all files and directories${N}";
echo -e "${R}             created during EZcast installation. ${N}";
echo " ";
read -p "Are you sure you want to uninstall EZcast ? [Y/n]: " choice;

if [ "$choice" != "n" ]; then 
    webspace_dir=$apache_documentroot; 
    default_repository_basedir="/var/lib/ezcast";
    default_documentroot="/var/www/";
    current_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd)";

    ###Check the basedir
    if [ "$repository_basedir" == "" ] || [ ! -d "$repository_basedir" ]; then
            correct_repo=0;
            while [ $correct_repo != 1  ]; do
                    read -p "Please, type the path to your repository [default:${default_repository_basedir}]: " repository_basedir;
                    if [ "$repository_basedir" == "" ]; then
                            repository_basedir=$default_repository_basedir;
                    fi;
                    if [ -d "$repository_basedir" ]; then
                            correct_repo=1;
                            break;	
                    fi;
                    echo -e "${R}$repository_basedir${N} is not a directory.";
                    repository_basedir=$default_repository_basedir;
            done;
    fi;
    echo -e "Repository_basedir :${G} $repository_basedir${N}";
    echo " ";

    check=0;
    while [[ $( basename $repository_basedir ) != "ezcast" && $check -lt 1 ]]; do
            read -p "Are you sure that your repository basedir is [$repository_basedir] ? [y/N]: " choice;
            if [ "$choice" == "y" ]; then
                    check=1;
            else
                    read -p "Enter the repository basedir [default: $default_repository_basedir]: " repository_basedir;
                     if [ "$repository_basedir" == "" ]; then
                                repository_basedir=$default_repository_basedir;
                     fi;
            fi;
    done;

    ###Delete the repository ?
    echo "Do you want to completely delete the repository ?";
    echo -e "${R}/!\ WARNING : This will delete any video file stored inside this folder.${N}";
    echo "If you choose not to delete the repository, video files will be moved in EZmanager."
    read -p "Do you want to delete video files ? [y/n]: " answer;
    if [ "$answer" == "y" ] || [ "$answer" == "yes" ] || [ "$answer" == "Y" ]; then 
            #Delete the repo
            echo -n "Deleting the repository...                        ";
            rm -rf $repository_basedir;
            if [ $? -ne 0 ]; then
                    echo -e "${R}Failed.${N}";
            else
                    echo -e "${G}Done.${N}";
            fi;
    else 
            if [ "$answer" == "n" ] || [ "$answer" == "no" ] || [ "$answer" == "N" ] || [ "$answer" == "" ]; then 
                    #Do not delete the repo
                    echo -e "The repository ${G}will not be deleted.${N}";
                    rm -rf $current_dir/ezmanager/ezcast_tree/repository
                    mv $repository_basedir/repository $current_dir/ezmanager/ezcast_tree/;
                    rm -rf $repository_basedir ;
                    echo -e "If needed, you can find the repository at : ";
                    echo -e "     ${G}$current_dir/ezmanager/ezcast_tree/repository/${N}";
            else
                    #Wrong answer : it won't be deleted
                    echo -e "Wrong answer : The repository ${R}will not be deleted.${N}.";
                    echo -e "If needed, you can find the repository at : ";
                    echo -e "     ${G}$repository_basedir/repository/${N}";
            fi;
    fi;

    ###Check the webspace
    if [ "$webspace_dir" == "" ] || [ ! -d "$webspace_dir" ]; then
            correct_repo=0;
            while [ $correct_repo != 1  ]; do
                    read -p "Please, type the path to your webspace [default:${default_documentroot}]: " webspace_dir;
                    if [ "$webspace_dir" == "" ]; then
                            webspace_dir=$default_documentroot;
                    fi;
                    if [ -d "$webspace_dir" ]; then
                            correct_repo=1;
                            break;	
                    fi;
                    echo -e "${R}$webspace_dir${N} is not a directory.";
                    webspace_dir=$default_mamp_webspace;
            done;
    fi;
    echo -e "Webspace :${G} $webspace_dir${N}";
    echo " ";

    ###Delete ezcast from the websace
    echo "Deleting ezcast from webspace...                  ";
    rm -rf ${webspace_dir}/ezadmin ;
    if [ $? -ne 0 ]; then
            echo -e "delete ${webspace_dir}/ezadmin - ${R}Failed.${N}"; 
    else
            echo -e "delete ${webspace_dir}/ezadmin - ${G}Done.${N}";
    fi;                 
    rm -rf ${webspace_dir}/ezmanager ;
    if [ $? -ne 0 ]; then
            echo -e "delete ${webspace_dir}/ezmanager - ${R}Failed.${N}"; 
    else
            echo -e "delete ${webspace_dir}/ezmanager - ${G}Done.${N}";
    fi;                  
    rm -rf ${webspace_dir}/ezplayer ;
    if [ $? -ne 0 ]; then
            echo -e "delete ${webspace_dir}/ezplayer - ${R}Failed.${N}"; 
    else
            echo -e "delete ${webspace_dir}/ezplayer - ${G}Done.${N}";
    fi;

    ###Delete the first_user file
    if [ -e ${current_dir}/first_user ]; then
        echo "Deleting the first user...                              ";
        rm -rf ${current_dir}/first_user;
        if [ $? -ne 0 ]; then
                echo -e "delete ${current_dir}/first_user - ${R}Failed.${N}";
        else
                echo -e "delete ${current_dir}/first_user - ${G}Done.${N}";
        fi;
    fi;

    ###Delete config files
    echo "Deleting config files...                          ";
    rm $current_dir/commons/config.inc;
    if [ $? -ne 0 ]; then
            echo -e "delete $current_dir/commons/config.inc - ${R}Failed.${N}";
    else
            echo -e "delete $current_dir/commons/config.inc - ${G}Done.${N}";
    fi;
    if [ -f "$current_dir/ezadmin/config.inc" ]; then
        rm $current_dir/ezadmin/config.inc;            
        if [ $? -ne 0 ]; then
                echo -e "delete $current_dir/ezadmin/config.inc - ${R}Failed.${N}";
        else
                echo -e "delete $current_dir/ezadmin/config.inc - ${G}Done.${N}";
        fi;
    fi;
    if [ -f "$current_dir/ezmanager/config.inc" ]; then
        rm $current_dir/ezmanager/config.inc;         
        if [ $? -ne 0 ]; then
                echo -e "delete $current_dir/ezmanager/config.inc - ${R}Failed.${N}";
        else
                echo -e "delete $current_dir/ezmanager/config.inc - ${G}Done.${N}";
        fi;
    fi;
    if [ -f "$current_dir/ezplayer/config.inc" ]; then
        rm $current_dir/ezplayer/config.inc;         
        if [ $? -ne 0 ]; then
                echo -e "delete $current_dir/ezplayer/config.inc - ${R}Failed.${N}";
        else
                echo -e "delete $current_dir/ezplayer/config.inc - ${G}Done.${N}";
        fi;
    fi;

    echo "";
    echo -e "${G}-- The end.${N}";
    echo " ";
else 
    echo "Goodbye";
    exit;
fi;
