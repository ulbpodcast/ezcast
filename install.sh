#!/bin/bash 

# EZCAST 
#
# Copyright (C) 2016 Université libre de Bruxelles
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

# includes localpaths 
source $(dirname $0)/commons/localpaths

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

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root (or sudo)" 1>&2
   exit 1
fi

echo "Press [Enter] to continue";
read whatever;
echo "First of all, this script will verify you have all programs,";
echo "commands and libraries required by EZcast to run properly";
echo " ";
echo "You can skip the tests if you want.";
echo -e "${R}Warning ! Skipping this verification may have critical repercussions${N}";
echo -e "${R}          on the use of EZcast after its installation.${N}";
echo " ";
read -p "Would you like to verify your server ? [y/n]: " choice;
if [ "$choice" != "n" ];
then
    check=1;
    echo "The script will now proceed to some verifications";
    echo " ";
    echo "*************************************************";
    echo "Verification for Apache2 ...";
    echo "*************************************************";
    # checks if apachectl_path is already in commons/localpaths
    if [ "$apachectl_path" != "" ]; then
        default_path=$apachectl_path;
    else 
        # apachectl_path is not in commons/localpaths so checks if it is in PATH
        cmd_path_find apachectl /usr/sbin/apachectl;
        default_path=$RES;
    fi;
    echo "Enter the path to 'apachectl' bin (with trailing 'apachectl'):";
    read -p "[default: $default_path]:" apachectl_path ;
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
        echo "If apache is installed, enter the path to the 'apachectl' bin (with trailing 'apachectl')";
        echo "Otherwise, please enter 'exit' to quit this script and install apache";
        read -p "[default: $default_path]:" apachectl_path;
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
    echo "apachectl_path=$apachectl_path" >> ./commons/localpaths;
    echo -e "${G}Apache is installed on your machine${N}";
    echo "";
    echo "*************************************************";
    echo "Verification for PHP ...";
    echo "*************************************************";
    
    if [ "$php_path" != "" ]; then
        default_path=$php_path;
    else 
        cmd_path_find php /usr/bin/php
        default_path=$RES
    fi;

    echo "Enter the path to 'php' bin (with trailing 'php')";
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
        echo "If PHP is installed, enter its path now (with trailing 'php')";
        echo "otherwise, please enter 'exit' to quit this script and install PHP";
        read -p "[default:$default_path]:" php_path;
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
    echo "php_path=$php_path" >> ./commons/localpaths;
    echo "-------------------------------------------------";
    echo -e "${G}PHP is installed${N}, verification of your version ...";
    echo "-------------------------------------------------";
    # Substring on the result of 'php -v'
    # 'php -v' always starts by 'PHP 5.x.x'
    version=${value:4:3};
    if [ $(expr $version '<' 5.3) -eq 1 ]; then
        echo -e "${R}You are using a deprecated version${N} of PHP [$version]. Update your version";
        echo "of PHP (at least 5.3) to ensure a good compatibility with EZcast";
        echo "Press [Enter] to quit this script or enter 'continue' to go to the";
        echo "next step anyway.";
        read choice;
        if [ "$choice" != "continue" ]; then exit; fi;
    else 
        echo -e "${G}Your current version of PHP [$version] matches EZcast's needs${N}";
    echo " ";
    fi;
    echo "*************************************************";
    echo "Verification of extensions for PHP ...";
    echo "*************************************************";
    read -p "Do you want to check PHP extensions ? [y/n]: " choice;
    if [ "$choice" != "n" ]; then
        # Verification for CURL
        check=0;
        while [ $check -lt 1 ]; do
            check=$($php_path -r "echo (function_exists('curl_version'))? 'enabled' : 'disabled';");
            if [[ "$check" == "disabled" ]]; then
                echo -e "${R}CURL seems not to be enabled for PHP.${N}";
                echo "Enable CURL for PHP and press [Enter] to retry.";
                read -p "Enter 'force' to continue without CURL enabled or 'quit' to leave: " choice;
                if [ "$choice" == "quit" ]; then exit; fi;
                check=0;
                if [ "$choice" == "force" ]; then check=1; fi;
            else 
                echo -e "${G}CURL is enabled for PHP${N}";
                check=1;
            fi;
        done;
        # Verification for SIMPLE_XML
        check=0;
        while [ $check -lt 1 ]; do
            check=$($php_path -r "echo (function_exists('simplexml_load_file'))? 'enabled' : 'disabled';");
            if [[ "$check" == "disabled" ]]; then
                echo -e "${R}SimpleXML seems not to be enabled for PHP.${N}";
                echo "Enable SimpleXML for PHP and press [Enter] to retry.";
                read -p "Enter 'force' to continue without SimpleXML enabled or 'quit' to leave: " choice;
                if [ "$choice" == "quit" ]; then exit; fi;
                check=0;
                if [ "$choice" == "force" ]; then check=1; fi;
            else 
                echo -e "${G}SimpleXML is enabled for PHP${N}";
                check=1;
            fi;
        done;
        # Verification for PDO
        check=0;
        while [ $check -lt 1 ]; do
            check=$($php_path -r "echo (extension_loaded('PDO') && extension_loaded('pdo_mysql'))? 'enabled' : 'disabled';");
            if [[ "$check" == "disabled" ]]; then
                echo -e "${R}PDO or pdo_mysql seems not to be enabled for PHP.${N}";
                echo "Enable PDO and pdo_mysql for PHP and press [Enter] to retry.";
                read -p "Enter 'force' to continue without PDO enabled or 'quit' to leave: " choice;
                if [ "$choice" == "quit" ]; then exit; fi;
                check=0;
                if [ "$choice" == "force" ]; then check=1; fi;
            else 
                echo -e "${G}PDO and pdo_mysql are enabled for PHP${N}";
                check=1;
            fi;
        done;
        # Verification for JSON
        check=0;
        while [ $check -lt 1 ]; do
            check=$($php_path -r "echo (function_exists('json_encode'))? 'enabled' : 'disabled';");
            if [[ "$check" == "disabled" ]]; then
                echo -e "${R}JSON seems not to be enabled for PHP.${N}";
                echo "Enable JSON for PHP and press [Enter] to retry.";
                read -p "Enter 'force' to continue without JSON enabled or 'quit' to leave: " choice;
                if [ "$choice" == "quit" ]; then exit; fi;
                check=0;
                if [ "$choice" == "force" ]; then check=1; fi;
            else 
                echo -e "${G}JSON is enabled for PHP${N}";
                check=1;
            fi;
        done;
    fi;
    echo "";
    echo "*************************************************";
    echo "Verification for RSYNC ...";
    echo "*************************************************";
    check=1;
    if [ "$rsync_path" != "" ]; then 
        default_path=$rsync_path;
    else 
        cmd_path_find rsync /usr/bin/rsync
        default_path=$RES
    fi;
    echo "Enter the path to 'rsync' bin (with trailing 'rsync')";
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
        echo "If RSYNC is installed, enter its path now (with trailing 'rsync').";
        echo "Otherwise, enter 'exit' to quit this script and install RSYNC";
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
    echo "rsync_path=$rsync_path" >> ./commons/localpaths;
    echo -e "${G}RSYNC is installed on your machine${N}";
    echo "";
    echo "*************************************************";
    echo "Verification for AT ...";
    echo "*************************************************";
    check=1;
    timer=45;
    echo test > at.tmp | at now;
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
                echo -e "You may need to enable it by executing: 'sudo launchctl load -F /System/Library/LaunchDaemons/com.apple.atrun.plist'"
    	    fi;
	else
	    timer=0;
            rm -rf ./at.tmp;
	    break;
    	fi;
    done;
    # Retry as long as AT has not been found
    while [ $check -lt 1 ]; do
        echo "If AT is installed, enter its path now (with trailing 'at').";
        echo "Otherwise, enter 'exit' to quit this script and install AT";
        read at_path;
        if [ "$at_path" == "exit" ]; then exit; fi;
        echo test > at.tmp | $at_path now
    	sleep 1
	timer=45
   	while [ $timer -gt 0 ]; do
    	    # Verify that a version of AT is installed
    	    if [ ! -f ./at.tmp ]; then
	        timer=`expr $timer - 1`
	        echo -n "."
	        sleep 1
	        if [ $timer == 0 ]; then
		    check=0
            	    echo -e "${R}AT does not seem to be installed at $at_path${N}"
    	        fi;
	    else
	        timer=0
                rm -rf ./at.tmp
	        break
    	    fi;
        done
    done
    echo " "
    echo -e "${G}AT is installed on your machine${N}"
    echo ""
    echo ""
    echo -e "${G}Congratulations, your server is ready to install EZcast and its components${N}"
    echo "Press [Enter] to install EZcast"
    read whatever
else 
    # tests have been skipped

    if [ "$apachectl_path" != "" ]; then
        default_apachectl_path=$apachectl_path;
    else 
        # apachectl_path is not in commons/localpaths so checks if it is in PATH
        cmd_path_find apachectl /usr/sbin/apachectl;
        default_apachectl_path=$RES;
    fi;
    echo "Enter the path to Apachectl (with trailing 'apachectl'):";
    read -p "[default: $default_apachectl_path]" apachectl_path;
    if [ "$apachectl_path" == "" ] 
    then 
	apachectl_path=$default_apachectl_path;
    fi;
    echo "apachectl_path=$apachectl_path" >> commons/localpaths;

    if [ "$php_path" != "" ]; then
        default_php_path=$php_path;
    else 
        cmd_path_find php /usr/bin/php
        default_php_path=$RES
    fi;
    echo "Enter the path to PHP (with trailing 'php'):";
    read -p "[default: $default_php_path]" php_path;
    if [ "$php_path" == "" ] 
    then 
	php_path=$default_php_path;
    fi;
    echo "php_path=$php_path" >> commons/localpaths;

    if [ "$rsync_path" != "" ]; then
        default_rsync_path=$rsync_path;
    else 
        cmd_path_find rsync /usr/bin/rsync 
        default_rsync_path=$RES
    fi;
    echo "Enter the path to RSYNC (with trailing 'rsync'):";
    read -p "[default: $default_rsync_path]" rsync_path;
    if [ "$rsync_path" == "" ] 
    then 
	rsync_path=$default_rsync_path;
    fi;
    echo "rsync_path=$rsync_path" >> commons/localpaths;
fi;

clear;

    echo "*************************************************";
    echo "Server configuration ...";
    echo "*************************************************";

echo -e "${G}EZcast will now be installed on the server.${N}";
echo " ";
echo "During the installation process, you will be requested";
echo "to enter some information such as path to specific directories";
echo "or user preferences.";
echo " ";
ezcast_basedir=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd );
echo "ezcast_basedir=$ezcast_basedir" >> ./commons/localpaths;
echo "Enter the path to your webspace (DocumentRoot).";
default_documentroot=`$apachectl_path -t -D DUMP_RUN_CFG|grep 'DocumentRoot'| awk '{print $3}'`
# the two next lines remove quotes at begin and end
default_documentroot="${default_documentroot%\"}"
default_documentroot="${default_documentroot#\"}"
if [ "$default_documentroot" == "" ]; then
    if [ -e "/var/www" ]; then
        default_documentroot="/var/www"
    fi;
fi;
while [[ -z $webspace_directory || ( ! -d "$webspace_directory" ) ]]; do
    read -p "[default: $default_documentroot]:" webspace_directory ;
    if [ -z "$webspace_directory" ];
    then
        webspace_directory=$default_documentroot
        if [ ! -d "$webspace_directory" ];
        then
            echo "Does not exists $webspace_directory"
        fi
    fi;
done;
echo "apache_documentroot=$webspace_directory" >> ./commons/localpaths

echo "EZmanager, EZadmin and EZplayer web interfaces will be placed"
echo "in subfolders of the $webspace_directory dir to be accessed"
echo "via a web browser."
echo " "
default_apache_username=`$apachectl_path -t -D DUMP_RUN_CFG|grep 'User:' | awk 'BEGIN {FS= "\""}{ print $2}'`
if [ "$default_apache_username" == "" ]; then
    if [ `grep _www /etc/passwd | wc -l` -ge 1 ]; then default_apache_username="_www"; fi;
    if [ `grep www-data /etc/passwd | wc -l` -ge 1 ]; then default_apache_username="www-data"; fi;
fi;
echo "Enter the username for Apache."
while [[ "$apache_username" == "" || "$choice" != "continue" ]]; do 
    read -p "[default: $default_apache_username]:" apache_username 
    if [ "$apache_username" == "" ];
    then
        apache_username=$default_apache_username
    fi;
    if [ `grep $apache_username /etc/passwd | wc -l` -lt 1 ]; then
        echo -e "${R}$apache_username user is not in /etc/passwd.${N}"
        read -p "Press [enter] to retry or enter 'continue' to skip this verification: " choice
    else 
        choice=continue
    fi;
done;
echo "apache_username=$apache_username" >> ./commons/localpaths

echo "Enter the path where you want to put the video repository "
echo "and the working directories (with trailing '/')."
echo "We recommend '/var/lib/' as the size of this directory will vary"
default_repository_path='/var/lib/'
read -p "[default: $default_repository_path]" repository_basedir
if [ "$repository_basedir" == "" ] 
then
    repository_basedir=$default_repository_path
fi;
# moves the different files where they are supposed to be
# moves repository and working directories 
mkdir -p $repository_basedir
repository_basedir=$repository_basedir/ezcast
echo "repository_basedir=$repository_basedir" >> ./commons/localpaths
cp -r $ezcast_basedir/ezmanager/ezcast_tree $repository_basedir
chown -R $apache_username $repository_basedir
#chgrp -R $apache_username $repository_basedir
chmod -R 755 $repository_basedir
echo "Enter a username and password for the web installer."
echo "These information will be requested by the web installer."
echo "The user you are now creating will be automatically set as the"
echo "first EZcast administrator. If you want to add other administrators"
echo "for EZcast, do it via EZadmin once EZcast is fully installed."
registration=0
while [ -z $username ];
do
    read -p "Username: " username
done

while [ "$registration" != "1" ]; do
    read -s -p "Password: " password;
    echo "";
    read -s -p "Password confirmation: " password_confirmation;
    if [ "$password" == "$password_confirmation" ]; then
	registration=1;
	echo "";
    else
	echo " "
	echo -e "${R}Password doesn't match, try again.${N}"
	echo ""
    fi;
done
echo ""
while [ "$firstname" == "" ]; do
    read -p "User's first name: " firstname
done;
while [ "$lastname" == "" ]; do
    read -p "User's last name: " lastname;
done;

$php_path $ezcast_basedir/cli_install.php "$php_path" "$rsync_path" "$webspace_directory" "$ezcast_basedir" "$repository_basedir" "$username" "$password" "$firstname" "$lastname" "$apache_username";

# set permissions for Apache user on EZcast files
chown -R $apache_username $ezcast_basedir;
chown -R $apache_username $webspace_directory/ezadmin;
chown -R $apache_username $webspace_directory/ezmanager;
chown -R $apache_username $webspace_directory/ezplayer;

chmod -R 755 $ezcast_basedir; 
echo " ";
cd commons/;

echo "*************************************************";
echo "SSH generation ...";
echo "*************************************************";
choice="";
while [[ ( ! -e `eval "echo ~$apache_username" ` ) && "$choice" != "continue" ]]; do
    echo -e "${R}Apache user doesn't have a home directory.${N} Add Apache user's home dir in /etc/passwd file and retry.";
    read -p "Press [Enter] to retry, enter 'continue' to continue without configuring SSH sharing and 'quit' to quit this installation: " choice;
    if [ "$choice" == "quit" ]; then exit; fi;
done; 

if [ "$choice" == "continue" ]; then
    echo -e "${R}SSH configuration has been skipped.${N} Make sure you generate SSH key for this server before adding a recorder and/or a renderer.";
    echo "Use 'ssh-keygen -t dsa' as $apache_username to generate an SSH key for Apache";
else 
    echo "this is a test file. It is aimed to be deleted. Do not hesitate to delete it." >> $webspace_directory/test_apache_homedir.killme;
    if [ -f `eval "echo ~$apache_username/test_apache_homedir.killme" ` ]; then 
        echo -e "${R}Warning: Apache user's homedir is the webspace. It means that the SSH public and private keys will be available to everybody. This is a serious security issue. Please change the homedir in /etc/passwd.${N}";
        read -p "Do you want to continue anyway ? [y/N]: " choice;
        if [ "$choice" == "y" ]; then
            htaccess="1";
        fi;
    else
        choice="y";
    fi;
    rm -rf $webspace_directory/test_apache_homedir.killme;
    if [ "$choice" == "y" ]; then
        if [ -e `eval "echo ~$apache_username/.ssh/id_dsa.pub" ` ]; then
            echo -e "${G}You have a valid SSH public key in Apache user's home dir.${N}";
            echo 'ssh_key_path='`eval "echo ~$apache_username/.ssh/id_dsa.pub" `>> localpaths;
        else 
            echo "";
            echo "You don't have an SSH public key yet. It is required for communications with EZrenderer and EZrecorder."
            read -p "Do you want to create it now ? [Y/n]: " choice
            if [ "$choice" != "n" ]; then
                echo -e "${G}Creating .ssh directory in `eval "echo ~$apache_username" ` ${N}";
                mkdir `eval "echo ~$apache_username" `/.ssh;
                echo -e "${G}Generating SSH keys in `eval "echo ~$apache_username" `/.ssh ${N}";
                echo -e  'y\n'|ssh-keygen -q -t dsa -N "" -f `eval "echo ~$apache_username" `/.ssh/id_dsa;
                chown -R $apache_username `eval "echo ~$apache_username" `/.ssh;
                chmod -R 755 `eval "echo ~$apache_username" `/.ssh;
                if [ "$htaccess" == "1" ]; then
                    # creates .htaccess in .ssh
                    if [ ! -e `eval "echo ~$apache_username/.ssh/.htaccess" ` ]; then
                        echo "deny from all" >> `eval "echo ~$apache_username/.ssh/.htaccess" `;
                    fi;
                fi;
                echo 'ssh_key_path='`eval "echo ~$apache_username/.ssh/id_dsa.pub" `>> localpaths;
            else 
                echo -e "${R}Do not forget to create an SSH key for this server before adding a recorder and/or a renderer.${N}";
                echo "Use 'ssh-keygen -t dsa' as $apache_username to generate an SSH key for Apache";
            fi;
        fi;
    else 
        echo "Modify Apache user's homedir in /etc/passwd and use 'ssh-keygen -t dsa' as $apache_username to generate an SSH key for Apache";
    fi;
fi; 

echo "";
echo -e "${G}EZcast is correctly installed !${N}";

echo " ";
echo "Continue the installation in the web installer accessible";
echo "from your web browser at the following address:";
echo "http://your.server.address/ezadmin";
echo " ";
echo "After finishing the web installation, please install";
echo "the EZrenderer before launching EZcast.";
