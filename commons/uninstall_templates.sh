#!/bin/bash
#NAME 		: uninstall_templates.sh
#DESCRIPTION	: remove folders and files created during templates installation
#AUTHOR		: podcastdev

G='\033[32m\033[1m'
R='\033[31m\033[1m'
N='\033[0m'

if [$1 == ""]; then
    echo -e "$0 :${R} Not enough args.${N}";
    echo -e "${G}usage:${N} use 1 or 0 to specify if the component should be deleted or not.";
    echo "       $0 <ezPlayer_value> <ezManager_value> <ezAdmin_value>";

    exit 0;
fi;

current_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd)";

clear;
echo "Welcome in this uninstallation script !";
echo " ";
echo "This script is aimed to clean after an ezCast's template installation:";
echo " ";
echo "*******************************************************************";
echo "*                                                                 *";
echo "*              T E M P L A T E S    C L E A N E R                 *";
echo "*                                                                 *";
echo "*******************************************************************";
echo -e "${R}/!\WARNING : This script use default templates path${N}:"
echo -e "${R}/path_ezplayer/tmpl/, /path_ezmanager/tmpl/ and /path_ezadmin/tmpl/${N}";
echo " ";
echo -e "${R}/!\WARNING : This script will delete ALL files inside${N}";
echo -e "${R}             'tmpl/' folders. ${N}";
echo " ";
read -p "Do you want to continue ? [yes/no]" answer;

if [ "$answer" == "y" ] || [ "$answer" == "yes" ] || [ "$answer" == "Y" ]; then

    if [ $1 == 1 ]; then
        echo -n "Deleting ezPlayer templates...                                 ";
        rm -rf $current_dir/../ezplayer/tmpl;
        if [ $? -ne 0 ]; then
            echo -e "${R}Failed.${N}";
        else
            echo -e "${G}Done.${N}";
        fi;
    fi;

    if [ $2 == 1 ]; then
        echo -n "Deleting ezManager templates...                                ";
        rm -rf $current_dir/../ezmanager/tmpl;
        if [ $? -ne 0 ]; then
            echo -e "${R}Failed.${N}";
        else
            echo -e "${G}Done.${N}";
        fi;
    fi;

    if [ $3 == 1 ]; then
        echo -n "Deleting ezAdmin templates...                                  ";
        rm -rf $current_dir/../ezadmin/tmpl;
        if [ $? -ne 0 ]; then
            echo -e "${R}Failed.${N}";
        else
            echo -e "${G}Done.${N}";
        fi;
    fi;

else
    echo "The templates won't be removed.";
fi;

echo " ";
echo -e "${G}-- The end.${N}";
