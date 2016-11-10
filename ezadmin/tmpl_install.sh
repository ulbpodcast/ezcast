#!/bin/bash

# EZCAST EZadmin 
# Copyright (C) 2016 Université libre de Bruxelles
#
# Written by Michel Jansens <mjansens@ulb.ac.be>
# 		    Arnaud Wijns <awijns@ulb.ac.be>
#                   Antoine Dewilde
#                   Thibaut Roskam
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

#NAME:          tmpl_install.sh
#DESCRIPTION:   Generate EZadmin's templates in all available languages
#AUTHOR:        Université libre de Bruxelles

# includes localpaths 
source $(dirname $0)/../commons/localpaths

G='\033[32m\033[1m'
R='\033[31m\033[1m'
N='\033[0m'

#ARG  - PHP_PATH from install_templates.sh in commons
php_cli_path=$1;
if [ "$php_cli_path" == "" ]; then php_cli_path=$php_path; fi;
if [ "$php_cli_path" == "" ]; then php_cli_path=php; fi;

current_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd)";

default_source_folder="$current_dir/tmpl_sources";
source_folder="";
default_dest_folder="$current_dir/tmpl";
dest_folder="";

echo "*******************************************************************";
echo "*                      EZADMIN TEMPLATES                          *";
echo "*******************************************************************";
echo "This script will now compile the templates in all available languages";
echo " ";

# echo "Please enter the path to template sources (relative path to the current folder, no trailing / please"
# echo "(default: $default_source_folder)"
# read source_folder;
# if [ -z "$source_folder" ]
# then
    source_folder=$default_source_folder;
# fi;
echo -e "Source folder: ${G}$source_folder${N}";
# echo " ";

# echo "Please enter the path to the folder you want the compiled templates to be put into (no trailing / please)"
# echo "(default: $default_dest_folder)"
# read dest_folder;
# if [ -z "$dest_folder" ]
# then
    dest_folder=$default_dest_folder;
# fi;
mkdir -p $dest_folder;
echo -e "Destination folder: ${G}$dest_folder${N}";
# echo " ";

# echo "Compiling files ..."
$php_cli_path $current_dir/../commons/cli_template_generate.php $source_folder fr $dest_folder $current_dir/translations.xml;
$php_cli_path $current_dir/../commons/cli_template_generate.php $source_folder en $dest_folder $current_dir/translations.xml;
echo "Compilation complete. Don't forget to edit config.inc to your own needs";