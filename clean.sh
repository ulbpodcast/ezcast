#!/bin/bash

# EZCAST 
#
# Copyright (C) 2014 Université libre de Bruxelles
#
# Written by Michel Jansens <mjansens@ulb.ac.be>
# 	      Arnaud Wijns <awijns@ulb.ac.be>
#            Antoine Dewilde
#            Carlos Avimadjessi
#
# This software is free software; you can redistribute it and/or
# modify it under the terms of the GNU Lesser General Public
# License as published by the Free Software Foundation; either
# version 3 of the License, or (at your option) any later version.
#
# This software is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
# Lesser General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public
# License along with this software; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

#NAME 		: clean.sh
#DESCRIPTION	: remove folders and files created during ezCast installation
#AUTHOR		: podcastdev

G='\033[32m\033[1m'
R='\033[31m\033[1m'
N='\033[0m'

clear;
echo "Welcome in this installation script !";
echo " ";
echo "This script is aimed to clean after an ezCast installation:";
echo " ";
echo "*******************************************************************";
echo "*                                                                 *";
echo "*                        C L E A N E R                            *";
echo "*                                                                 *";
echo "*******************************************************************";
echo -e "${R}/!\WARNING : This script will delete all files and directories${N}";
echo -e "${R}             created during ezCast installation. ${N}";
echo " ";
repository_basedir=$1;
webspace_dir=$2; 
default_mamp_webspace=/Applications/MAMP/htdocs;
default_repository_basedir="/var/lib";
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
repository_basedir=${repository_basedir}/ezcast;
echo -e "Repository_basedir :${G} $repository_basedir${N}";
echo " ";

###Check the webspace
if [ "$webspace_dir" == "" ] || [ ! -d "$webspace_dir" ]; then
	correct_repo=0;
	while [ $correct_repo != 1  ]; do
		read -p "Please, type the path to your webspace [default:${default_mamp_webspace}]: " webspace_dir;
		if [ "$webspace_dir" == "" ]; then
			webspace_dir=$default_mamp_webspace;
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
echo -n "Deleting ezcast from webspace...                  ";
rm -rf ${webspace_dir}/ez* ;
if [ $? -ne 0 ]; then
	echo -e "${R}Failed.${N}"; 
else
	echo -e "${G}Done.${N}";
fi;

###Delete the first_user file
echo -n "Deleting the user...                              ";
rm -rf ${current_dir}/first_user;
if [ $? -ne 0 ]; then
	echo -e "${R}Failed.${N}";
else
	echo -e "${G}Done.${N}";
fi;

###Delete config files
echo -n "Deleting config files...                          ";
rm $current_dir/commons/config.inc;
if [ -f "$current_dir/ezplayer/config.inc" ]; then
	rm $current_dir/ezmanager/config.inc;
	rm $current_dir/ezplayer/config.inc;
	rm $current_dir/ezadmin/config.inc;
fi;
if [ $? -ne 0 ]; then
    	echo -e "${R}Failed.${N}";
else
	echo -e "${G}Done.${N}";
fi;

###Delete the repository ?
echo "Should we delete the repository ? [yes/no]";
echo -e "${R}/!\ WARNING : This will delete any video file stored inside this folder.${N}";
read answer;
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
		mv $repository_basedir/repository $current_dir/ezmanager/ezcast_tree/.;
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

echo "";
echo -e "${G}-- The end.${N}";
echo " ";
