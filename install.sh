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

#try to find fullpath of command if in path or returns default value
cmd_path_find()
{
  COMMAND=$1
  DEFAULT=$2
  cmdpath=`which $1`
  if [ "$?" -eq "0" ]; then 
    RES=$cmdpath
    return 0
   else
    RES=$DEFAULT  
    return 0
  fi
}

G='\033[32m\033[1m'
R='\033[31m\033[1m'
N='\033[0m'

clear;
echo "Welcome in this installation script !";
echo " ";
echo "This script is aimed to install the following components of EZcast:";
echo " ";
echo "*******************************************************************";
echo "*                                                                 *";
echo "*      - EZadmin   : Handles users and courses that have          *";
echo "*                    access to EZcast.                            *";
echo "*      - EZmanager : Let the user manage his albums and           *";
echo "*                    recordings.                                  *";
echo "*      - EZplayer  : Allow users to watch the videos in           *";
echo "*                    in a rich video player through their         *";
echo "*                    web browser (require html5)                  *";
echo "*                                                                 *";
echo "*******************************************************************";
echo " ";
echo "Press [Enter] to continue";
read whatever;
echo "First of all, this script will verify you have all programs,";
echo "commands and libraries required by EZcast to run properly";
echo " ";
echo "Press [Enter] to continue or [n] to skip this step.";
echo -e "${R}Warning ! Skipping this verification may have critical repercussions${N}";
echo -e "${R}          on the use of EZcast after its installation.${N}";
echo " ";
read choice;
if [ "$choice" != "n" ];
then
    check=1;
    echo "The script will now proceed to some verifications";
    echo " ";
    echo "*************************************************";
    echo "Verification for Apache2 ...";
    echo "*************************************************";
    cmd_path_find apachectl /usr/sbin/apachectl 
    default_path=$RES
    echo "Please enter the path to 'apachectl' bin (with tailing 'apachectl'):";
    read -p "[default:$default_path]:" apachectl_path ;
    if [ "$apachectl_path" == "" ];
    then
    	apachectl_path=$default_path;
    fi;
    value=$($apachectl_path -version | grep 'version');
    # Verify that a version of Apache is installed
    if [[ "$value" != *version* ]]; then
        check=0;
        echo -e "${R}Apache does not seem to be installed at $apachectl_path${N}";
    fi;
    # Retry as long as apachectl has not been found
    while [ $check -lt 1 ]; do
	apachectl_path=$default_path;
        echo "If apache is installed, please enter the path to the 'apachectl' bin (with tailing 'apachectl')";
        echo "Otherwise, please enter 'exit' to quit this script and install apache";
        read apachectl_path;
        if [ "$apachectl_path" == "exit" ]; 
	    then exit; 
	fi;
        if [ "$apachectl_path" == "" ];
        then
    	    apachectl_path=$default_path;
        fi;
        value=$( $apachectl_path -version | grep version );
        if [[ "$value" == *version* ]]; then
            check=1;
        fi;
    done;
    echo -e "${G}Apache is installed on your machine${N}";
    echo "";
    echo "Press [Enter] to continue";
    read whatever;
    echo " ";
    echo "*************************************************";
    echo "Verification for PHP ...";
    echo "*************************************************";
    cmd_path_find php /usr/bin/php
    default_path=$RES

    echo "Please enter the path to 'php' bin (with tailing 'php')";
    read -p "[default:$default_path]:" php_path ;
    if [ "$php_path" == "" ];
    then
    	php_path=$default_path;
    fi;
    value=$( $php_path -v);
    # Verify that a version of PHP is installed
    if [[ "$value" != PHP* ]]; then
        check=0;
        echo -e "${R}PHP does not seem to be installed at $php_path${N}" ;
    fi;
    # Retry as long as PHP has not been found
    while [ $check -lt 1 ]; do
        echo "If PHP is installed, please enter its path now (with tailing 'php')";
        echo "otherwise, please enter 'exit' to quit this script and install PHP";
        read php_path;
        if [ "$php_path" == "exit" ]; then exit; fi;
        if [ "$php_path" == "" ];
        then
    	    php_path=$default_path;
        fi;
        value=$( $php_path -v );
        if [[ "$value" == PHP* ]]; then
            check=1;
        fi;
	php_path=$default_path;
    done;
    echo "-------------------------------------------------";
    echo -e "${G}PHP is installed${N}, verification of your version ...";
    echo "-------------------------------------------------";
    # Substring on the result of 'php -v'
    # 'php -v' always starts by 'PHP 5.x.x'
    version=${value:4:3};
    if [ $(expr $version '<' 5.3) -eq 1 ]; then
        echo -e "${R}You are using a deprecated version${N} of PHP [$version]. Please update your version";
        echo "of PHP (at least 5.3) to ensure a good compatibility with EZcast";
        echo "Press [Enter] to quit this script or enter 'continue' to go to the";
        echo "next step anyway.";
        read choice;
        if [ "$choice" != "continue" ]; then exit; fi;
    fi;
    echo -e "${G}Your current version of PHP [$version] matches EZcast's needs${N}";
    echo " ";
    echo "Press [Enter] to continue";
    read whatever;
    echo " ";
    echo "*************************************************";
    echo "Verification of LDAP extension for PHP ...";
    echo "*************************************************";
    echo "";
    echo "The script will now test if the ldap extension for PHP is enabled.";
    echo "Press [Enter] to continue or enter 'skip' to skip the test.";
    read choice;
    if [ "$choice" != "skip" ]; then
        check=$($php_path -r "echo (function_exists('ldap_connect'))? 'enabled' : 'disabled';");
        if [[ "$check" == "disabled" ]]; then
            echo "${R}LDAP seems not to be enabled for PHP.${N}";
            echo "Press [Enter] to quit this script or enter 'continue' to go to the";
            echo "next step anyway.";
            read choice;
            if [ "$choice" != "continue" ]; then exit; fi;
        fi;
        if [ "$choice" != "continue" ]; then echo -e "${G}LDAP is enabled for PHP${N}"; fi;
    fi;
    echo "*************************************************";
    echo "Verification of CURL extension for PHP ...";
    echo "*************************************************";
    echo "";
    echo "The script will now test if the curl extension for PHP is enabled.";
    echo "Press [Enter] to continue or enter 'skip' to skip the test.";
    read choice;
    if [ "$choice" != "skip" ]; then
        check=$($php_path -r "echo (function_exists('curl_version'))? 'enabled' : 'disabled';");
        if [[ "$check" == "disabled" ]]; then
            echo -e "${R}CURL seems not to be enabled for PHP.${N}";
            echo "Press [Enter] to quit this script or enter 'continue' to go to the";
            echo "next step anyway.";
            read choice;
            if [ "$choice" != "continue" ]; then exit; fi;
        fi;
        if [ "$choice" != "continue" ]; then echo -e "${G}CURL is enabled for PHP${N}"; fi;
    fi;
    echo "*************************************************";
    echo "Verification of MySQL extension for PHP ...";
    echo "*************************************************";
    echo "";
    echo "The script will now test if the MySQL extension for PHP is enabled.";
    echo "Press [Enter] to continue or enter 'skip' to skip the test.";
    read choice;
    if [ "$choice" != "skip" ]; then
        check=$($php_path -r "echo (function_exists('mysql_connect'))? 'enabled' : 'disabled';");
        if [[ "$check" == "disabled" ]]; then
            echo -e "${R}MySQL seems not to be enabled for PHP.${N}";
            echo "Press [Enter] to quit this script or enter 'continue' to go to the";
            echo "next step anyway.";
            read choice;
            if [ "$choice" != "continue" ]; then exit; fi;
        fi;
        if [ "$choice" != "continue" ]; then echo -e "${G}MySQL is enabled for PHP${N}"; fi;
    fi;
    echo "*************************************************";
    echo "Verification of APC extension for PHP ...";
    echo "*************************************************";
    echo "";
    echo "The script will now test if the APC extension for PHP is enabled.";
    echo "Press [Enter] to continue or enter 'skip' to skip the test.";
    read choice;
    if [ "$choice" != "skip" ]; then
        check=$($php_path -r "echo (function_exists('apc_fetch'))? 'enabled' : 'disabled';");
        if [[ "$check" == "disabled" ]]; then
            echo -e "${R}APC seems not to be enabled for PHP.${N} APC is not strictly neccessary but improves performance. It can also be replaced by PHP's OPCache";
            echo "Press [Enter] to continue this script";
            read choice;
            #if [ "$choice" != "continue" ]; then exit; fi;
        else
        if [ "$choice" != "continue" ]; then echo -e "${G}APC is enabled for PHP${N}"; fi;
        fi;
    fi;
    echo "*************************************************";
    echo "Verification of SimpleXML extension for PHP ...";
    echo "*************************************************";
    echo "";
    echo "The script will now test if the SimpleXML extension for PHP is enabled.";
    echo "Press [Enter] to continue or enter 'skip' to skip the test.";
    read choice;
    if [ "$choice" != "skip" ]; then
        check=$($php_path -r "echo (function_exists('simplexml_load_file'))? 'enabled' : 'disabled';");
        if [[ "$check" == "disabled" ]]; then
            echo -e "${R}SimpleXML seems not to be enabled for PHP.${N}";
            echo "Press [Enter] to quit this script or enter 'continue' to go to the";
            echo "next step anyway.";
            read choice;
            if [ "$choice" != "continue" ]; then exit; fi;
        fi;
        if [ "$choice" != "continue" ]; then echo -e "${G}SimpleXML is enabled for PHP${N}"; fi;
    fi;
    echo "*************************************************";
    echo "Verification of JSON extension for PHP ...";
    echo "*************************************************";
    echo "";
    echo "The script will now test if the JSON extension for PHP is enabled.";
    echo "Press [Enter] to continue or enter 'skip' to skip the test.";
    read choice;
    if [ "$choice" != "skip" ]; then
        check=$($php_path -r "echo (function_exists('json_encode'))? 'enabled' : 'disabled';");
        if [[ "$check" == "disabled" ]]; then
            echo -e "${R}JSON seems not to be enabled for PHP.${N}";
            echo "Press [Enter] to quit this script or enter 'continue' to go to the";
            echo "next step anyway.";
            read choice;
            if [ "$choice" != "continue" ]; then exit; fi;
        fi;
        if [ "$choice" != "continue" ]; then echo -e "${G}JSON is enabled for PHP${N}"; fi;
    fi;
    echo "*************************************************";
    echo "Verification for RSYNC ...";
    echo "*************************************************";
    check=1;
    cmd_path_find rsync /usr/bin/rsync
    default_path=$RES
    echo "Please enter the path to 'rsync' bin (with tailing 'rsync')";
    read -p "[default: $default_path]:" rsync_path ;
    if [ "$rsync_path" == "" ];
    then
    	rsync_path=$default_path;
    fi;
    value=$( $rsync_path --version | grep 'version');
    # Verify that a version of rsync is installed
    if [[ "$value" != *version* ]]; then
        check=0;
        echo -e "${R}RSYNC does not seem to be installed at $rsync_path${N}";
    fi;
    # Retry as long as rsync has not been found
    while [ $check -lt 1 ]; do
        echo "If RSYNC is installed, please enter its path now (with tailing 'rsync').";
        echo "Otherwise, please enter 'exit' to quit this script and install RSYNC";
        read rsync_path;
        if [ "$rsync_path" == "exit" ]; then exit; fi;
        if [ "$rsync_path" == "" ];
        then
    	    rsync_path=$default_path;
        fi;
        value=$( $rsync_path --version | grep version );
        if [[ "$value" == *version* ]]; then
            check=1;
        fi;
    done;
    echo -e "${G}RSYNC is installed on your machine${N}";
    echo "";
    echo "Press [Enter] to continue";
    read whatever;
    echo "*************************************************";
    echo "Verification for AT ...";
    echo "*************************************************";
    check=1;
    timer=45;
    echo "echo test > at.tmp " | at now;
    sleep 1;
    while [ $timer -gt 0 ]; do
    	# Verify that a version of AT is installed
    	if [ ! -f ./at.tmp ]; then
	    let timer--;
	    echo -n ".";
	    sleep 1;
	    if [ $timer == 0 ]; then
		check=0;
            	echo -e "${R}AT does not seem to be installed or its path is not set in PATH var${N}";
    	    fi;
	else
	    timer=0;
            rm -rf ./at.tmp;
	    break;
    	fi;
    done;
    # Retry as long as AT has not been found
    while [ $check -lt 1 ]; do
        echo "If AT is installed, please enter its path now (with tailing 'at').";
        echo "Otherwise, please enter 'exit' to quit this script and install AT";
        read at_path;
        if [ "$at_path" == "exit" ]; then exit; fi;
        echo "echo test > at.tmp | $at_path now";
    	sleep 1;
	timer=45;
   	while [ $timer -gt 0 ]; do
    	    # Verify that a version of AT is installed
    	    if [ ! -f ./at.tmp ]; then
	        timer=$timer - 1;
	        echo -n ".";
	        sleep 1;
	        if [ $timer == 0 ]; then
		    check=0;
            	    echo -e "${R}AT does not seem to be installed at $at_path${N}";
    	        fi;
	    else
	        timer=0;
                rm -rf ./at.tmp;
	        break;
    	    fi;
        done;
    done;
    echo " ";
    echo -e "${G}AT is installed on your machine${N}";
    echo "";
    echo "Press [Enter] to continue";
    read whatever;
    echo -e "${G}Congratulations, your server is ready to install EZcast and its components${N}";
    echo "Press [Enter] to install EZcast";
    read whatever;
else 
    default_php_path="/usr/bin/php";
    echo "Please, enter the path to PHP (with tailing 'php'):";
    read -p "[default: $default_php_path]" php_path;
    if [ "$php_path" == "" ] 
    then 
	php_path=$default_php_path;
    fi;
    cmd_path_find rsync /usr/bin/rsync 
    default_rsync_path=$RES
    echo "Enter the path to RSYNC (with tailing 'rsync'):";
    read -p "[default: $default_rsync_path]" rsync_path;
    if [ "$rsync_path" == "" ] 
    then 
	rsync_path=$default_rsync_path;
    fi;
fi;

clear;
echo -e "${G}EZcast will now be installed on the server.${N}";
echo " ";
echo "During the installation process, you will be requested";
echo "to enter some information such as path to specific directories";
echo "or user preferences.";
echo " ";
ezcast_basedir=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd );
echo "Please, enter the path to your webspace (DocumentRoot).";
default_documentroot=`$apachectl_path -t -D DUMP_RUN_CFG|grep 'DocumentRoot'|awk '{print $3}'`
read -p "[default: $default_documentroot]:" webspace_directory ;

echo "EZmanager, EZadmin and EZplayer web interfaces will be placed";
echo "in subfolders of the $webspace_directory dir to be accessed";
echo "via a web browser.";
echo " ";
default_apache_username=`ps -ef | grep httpd | grep -v grep | head -1 | awk '{print $1}'`
echo "Please, enter the username for Apache.";
echo "(typically 'www-data' or '_www')";
    read -p "[default: $default_apache_username]:" apache_username ;
    if [ "$apache_username" == "" ];
    then
        apache_username=$default_apache_username;
    fi;

echo "Please, enter the path where you want to put the video repository ";
echo "and the working directories (with tailing '/').";
echo "We recommend '/var/lib/' as the size of this directory will vary";
default_repository_path='/var/lib/';
read -p "[default: $default_repository_path]" repository_basedir;
if [ "$repository_basedir" == "" ] 
then
    repository_basedir=$default_repository_path;
fi;
# moves the different files where they are supposed to be
# moves repository and working directories 
mkdir -p $repository_basedir;
repository_basedir=$repository_basedir/ezcast;
#mv $ezcast_basedir/ezmanager/ezcast_tree $repository_basedir;
cp -r $ezcast_basedir/ezmanager/ezcast_tree $repository_basedir;
chown -R $apache_username $repository_basedir;
#chgrp -R $apache_username $repository_basedir;
chmod -R 755 $repository_basedir;
echo "Please, enter a username and password for the web installer.";
echo "These information will be requested by the web installer.";
echo "The user you are now creating will be automatically set as the";
echo "first EZcast administrator. If you want to add other administrators";
echo "for EZcast, do it via EZadmin once EZcast is fully installed.";
registration=0;
read -p "Username: " username;
while [ "$registration" != "1" ]; do
    read -s -p "Password: " password;
    echo "";
    read -s -p "Password confirmation: " password_confirmation;
    if [ "$password" == "$password_confirmation" ]; then
	registration=1;
	echo "";
    else
	echo " ";
	echo -e "${R}Password doesn't match, please try again.${N}";
	echo "";
    fi;
done;
echo "";
while [ "$firstname" == "" ]; do
    read -p "User's first name: " firstname;
done;
while [ "$lastname" == "" ]; do
    read -p "User's last name: " lastname; 
done;
$php_path $ezcast_basedir/cli_install.php "$php_path" "$rsync_path" "$ezcast_basedir" "$repository_basedir" "$username" "$password" "$firstname" "$lastname";
# set permissions for Apache user on EZcast files
chown -R $apache_username $ezcast_basedir;
#chgrp -R $apache_username $ezcast_basedir;
chmod -R 755 $ezcast_basedir;
# places web files in the webspace
cp -rp $ezcast_basedir/ezadmin/htdocs $webspace_directory/ezadmin;
cp -rp $ezcast_basedir/ezmanager/htdocs $webspace_directory/ezmanager;
cp -rp $ezcast_basedir/ezplayer/htdocs $webspace_directory/ezplayer;
echo " ";
cd commons/;
./install_templates.sh 1 1 1;
echo "";
echo -e "${G}EZcast is correctly installed !${N}";

echo " ";
echo "Continue the installation in the web installer accessible";
echo "from your web browser at the following address:";
echo "http://your.server.address/ezadmin";
echo " ";
echo "After finishing the web installation, please install";
echo "the EZrenderer before launching EZcast.";
