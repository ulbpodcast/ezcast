#!/bin/bash

# EZCAST EZrenderer
#
# Copyright (C) 2014 Université libre de Bruxelles
#
# Written by Michel Jansens <mjansens@ulb.ac.be>
# 	     Arnaud Wijns <awijns@ulb.ac.be>
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


G='\033[32m\033[1m'
R='\033[31m\033[1m'
N='\033[0m'

nb_errors=0;
clear;
echo "Welcome in this installation script !";
echo " ";
echo "This script is aimed to install the following components of EZcast:";
echo " ";
echo "*******************************************************************";
echo "*                                                                 *";
echo "*      - EZrenderer :                                             *";
echo "*                                                                 *";
echo "*******************************************************************";
echo " ";
echo "Press [Enter] to continue";
read whatever;
echo "First of all, this script will verify you have all programs,";
echo "commands and libraries required by EZrenderer to run properly";
echo " ";
echo "Press [Enter] to continue or [n] to skip this step.";
echo -e "${R}Warning ! Skipping this verification may have critical repercussions${N}";
echo -e "${R}          on the use of EZrenderer after its installation.${N}";
echo " ";
read choice;
if [ "$choice" != "n" ];
then
    check=1;
    echo "The script will now proceed to some verifications";
    echo " ";
    echo "*************************************************";
    echo "Verification for PHP5 ...";
    echo "*************************************************";
    default_path="/usr/bin/php";
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
        echo "If PHP5 is installed, please enter its path now (with tailing 'php')";
        echo "otherwise, please enter 'exit' to quit this script and install PHP5";
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
    echo "Verification of SimpleXML extension for PHP5 ...";
    echo "*************************************************";
    echo "";
    echo "The script will now test if the SimpleXML extension for PHP is enabled.";
    echo "Press [Enter] to continue or enter 'skip' to skip the test.";
    read choice;
    if [ "$choice" != "skip" ]; then
        check=$($php_path -r "echo (function_exists('simplexml_load_file'))? 'enabled' : 'disabled';");
        if [[ "$check" == "disabled" ]]; then
            echo -e "${R}SimpleXML seems not to be enabled for PHP5.${N}";
            echo "Press [Enter] to quit this script or enter 'continue' to go to the";
            echo "next step anyway.";
            read choice;
            if [ "$choice" != "continue" ]; then exit; fi;
        fi;
        if [ "$choice" != "continue" ]; then echo -e "${G}SimpleXML is enabled for PHP5${N}"; fi;
    fi;
    echo " ";
    echo "*************************************************";
    echo "Verification of lib-gd";
    echo "*************************************************";
    echo "";
    echo "Testing if lib-gd is installed for PHP...";
    echo "<?php var_dump(gd_info()) ?>" > lib_gd_test.php;
    test_value=$( $php_path "lib_gd_test.php" );
    if [[  "$test_value" == array* ]]; then
        echo -e "${G}lib-gd is installed${N}";
        echo "Testing if FreeType is supported by lib-gd";
        if [ "$test_value"["FreeType Support"] ]; then
            echo -e "${G}FreeType is supported${N}";
            rm lib_gd_test.php;
        else
            echo -e "${R}FreeType is not supported${N}";
        fi;
    else
        echo -e "${R}lib-gd is not installed${N}";
    fi;
    echo " ";
    echo "*************************************************";
    echo "Verification of FFPROBE";
    echo "*************************************************";
    echo "";
    default_path="/usr/local/bin/ffprobe";
    echo "Please enter the path to 'ffprobe' bin (with tailing 'ffprobe')";
    read -p "[default:$default_path]:" ffprobe_path ;
    if [ "$ffprobe_path" == "" ]; then
        ffprobe_path=$default_path;
    fi
    echo "Testing if ffprobe is installed...";
    test_value=$( $ffprobe_path -version );
    if [[  "$test_value" == ffprobe* ]]; then
        check=1;
        echo -e "${G}ffprobe is installed${N}";
    else
        check=0;
        echo -e "${R}ffprobe is not installed at '$ffprobe_path'${N}";
    fi;
    while [ $check -lt 1 ]; do
        echo "If FFPROBE is installed, please enter its path now (with tailing 'ffprobe')";
        echo "otherwise, please enter 'exit' to quit this script and install ffprobe";
        read ffprobe_path;
        if [ "$ffprobe_path" == "exit" ]; then exit; fi;
        if [ "$ffprobe_path" == "" ];
        then
            ffprobe_path=$default_path;
        fi;
         test_value=$( $ffprobe_path -version );

        if [[  "$test_value" == ffprobe* ]]; then
            check=1;
            echo -e "${G}ffprobe is installed${N}";
        else            check=0;
            echo -e "${R}ffprobe is not installed at '$ffprobe_path'${N}";
        fi;
    ffprobe_path=$default_path;
    done;
    echo " ";
    echo "*************************************************";
    echo "Verification of FFMPEG";
    echo "*************************************************";
    echo "";
    default_path="/usr/local/bin/ffmpeg";
    echo "Please enter the path to 'ffmpeg' bin (with tailing 'ffmpeg')";
    read -p "[default:$default_path]:" ffmpeg_path ;
    if [ "$ffmpeg_path" == "" ]; then
        ffmpeg_path=$default_path;
    fi
    echo "Testing if ffmpeg is installed...";
    test_value=$( $ffmpeg_path -version );
    if [[  "$test_value" == ffmpeg* ]]; then
        check=1; 
        echo -e "${G}FFMPEG is installed${N}";
        echo "Testing if 'aac' codec is supported by FFMPEG..."
        test_value=$( $ffmpeg_path -codecs | grep 'libfdk_aac' );
        if [[ "$test_value" == *AAC* ]]; then
            echo -e "${G}AAC coded is supported${N}";
        else
            echo -e "${R}AAC codec is not supported${N}";
        fi;
        echo "Testing if 'h264' codec is supported by FFMPEG..."
        test_value=$( $ffmpeg_path -codecs | grep 'h264' );
        if [[ "$test_value" == *H.264* ]]; then
            echo -e "${G}H.264 codec is supported${N}";
        else
            echo -e "${R}H.264 codec is not supported${N}";
        fi;
    else
        check=0;
        echo -e "${R}FFMPEG is not installed at $ffmpeg_path${N}";
    fi;
    while [ $check -lt 1 ]; do
        echo "If FFMPEG is installed, please enter its path now (with tailing 'ffmpeg')";
        echo "otherwise, please enter 'exit' to quit this script and install ffmpeg";
        read ffmpeg_path;
        if [ "$ffmpeg_path" == "exit" ]; then exit; fi;
        if [ "$ffmpeg_path" == "" ]; then
            ffmpeg_path=$default_path;
        fi;
        test_value=$( $ffmpeg_path -version );
        if [[  "$test_value" == ffmpeg* ]]; then
            check=1;
            echo -e "${G}FFMPEG is installed${N}";
            echo "Testing if 'aac' codec is supported by FFMPEG..."
            test_value=$( $ffmpeg_path -codecs | grep 'libfdk_aac' );
            if [[ "$test_value" == *AAC* ]]; then
                echo -e "${G}AAC coded is supported${N}";
            else
                echo -e "${R}AAC codec is not supported${N}";
            fi;
            echo "Testing if 'h264' codec is supported by FFMPEG..."
            test_value=$( $ffmpeg_path -codecs | grep 'h264' );
            if [[ "$test_value" == *H.264* ]]; then
                echo -e "${G}H.264 codec is supported${N}";
            else
                echo -e "${R}H.264 codec is not supported${N}";
            fi;
        fi;
        ffmpeg_path=$default_path;
    done;
    echo " ";
else 
    default_php_path="/usr/bin/php";
    echo "Please, enter the path to PHP5 (with tailing 'php'):";
    read -p "[default: $default_php_path]" php_path;
    if [ "$php_path" == "" ]; then 
        php_path=$default_php_path;
    fi;
fi;
echo " ";
echo "*******************************************************************";
echo "*                         C  O  N  F  I  G                        *";
echo "*******************************************************************";
echo "";
echo "Please enter now the requested values: ";
echo "-------------------------------------------------------------------";

default_threads_number=4;
read -p "Number of threads [1-9] [default : $default_threads_number] : " threads_number;
if [ "$threads_number" == "" ] || [[ $threads_number != [1-9] ]]; then
    threads_number=$default_threads_number;
fi;
echo -e "${G}Threads number : $threads_number${N}";
echo " ";

default_max_jobs=4;
read -p "Max jobs before saturation [1-9] [default : $default_max_jobs] : " max_jobs;
if [ "$max_jobs" == "" ] || [[ $max_jobs != [1-9] ]]; then
    max_jobs=$default_max_jobs;
fi;
echo -e "${G}Max jobs       : $max_jobs${N}";
echo " ";

# echo "The font used to render the titling at the beginning of the videos";
# default_title_font="/Library/Fonts/Tohama.ttf";
# read -p "Title font path [default : $default_title_font] : " title_font;
# if [ "$title_font" == "" ]; then
#     title_font=$default_title_font;
# fi;
# echo -e "${G}Font path      : $title_font${N}";
# echo " ";
 
# echo "The ratio for the font in the titling. It depends on the default font size.";
# echo "Higher the ratio, smaller the font is.";
# default_font_ratio=1.0;
# read -p "Font size ratio [0.1-1.9] [default : $default_font_ratio]" font_ratio;
# if [ "$font_ratio" == "" ] || [[ $font_ratio != [0-1][.][0-9] ]]; then
#     font_ratio=$default_font_ratio;
# fi;
# echo -e "${G}Font ratio     : $font_ratio${N}";
# echo " ";

# default_jpg_quality=100;
# echo "The quality for jpg compression has an impact on the titling.";
# echo "Higher the quality, better the titling is.";
# read -p "Quality for jpg compression [0-100] [default : $default_jpg_quality]" jpg_quality;
# if [ "$jpg_quality" == "" ] || [ $jpg_quality -lt 000 ] || [ $jpg_quality -gt 100 ] || [[ $jpg_quality != [0-1][0-9][0-9] ]]; then
#    jpg_quality=$default_jpg_quality;
# fi;
# echo -e "${G}JPG Quality    : $jpg_quality${N}";
# echo " ";

# default_line_length=35;
# read -p "Number of characters per line [10-40] [default : $default_line_length]" line_length;
# if [ "$line_length" == "" ] || [ $line_length -lt 10 ] || [ $line_length -gt 40 ] || [[ $line_length != [1-4][0-9] ]]; then
#     line_length=$default_line_length;
# fi;
# echo -e "${G}Line length    : $line_length${N}";
# echo " ";

echo -n "Creating the config file...";
$php_path renderer_install.php "$php_path" "$ffmpeg_path" "$ffprobe_path" "$threads_number" "$max_jobs";
echo -e "${G}                                  Done${N}"
echo " ";
echo -e "${G}-- The end.${N}";
