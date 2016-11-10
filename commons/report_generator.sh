#!/bin/sh 

# EZCAST
#
# Copyright (C) 2015 Université libre de Bruxelles
#
# Written by Michel Jansens <mjansens@ulb.ac.be>
# 	     Arnaud Wijns <awijns@ulb.ac.be>
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

# This script generates a report on EZcast usages.

Print_help(){
    echo ""
    echo "Ce script utilise 2 dates pour définir l'intervalle de recherche."
    echo "Seuls les assets créés entre ces deux dates seront repris dans "
    echo "le rapport."
    echo "Il est dès lors facile de créer un rapport pour une année académique"
    echo "ou pour un quadrimestre (ex: -min_date 20150915 -max_date 20160630)"
    echo ""
    echo "Utilisation des options:"
    echo "''''''''''''''''''''''''"
    echo "-s | -start     : définit la date de début de recherche (obligatoire)"
    echo "                  la date utilise le format YYYYMMDD"
    echo "-e | -end       : définit la date de fin de recherche (obligatoire)"
    echo "                  la date utilise le format YYYYMMDD"
    echo "-m | -ezmanager : définit s'il faut afficher ou non les informations"
    echo "        ^         relatives à EZmanager."
    echo "                  valeurs: true (defaut) | false"
    echo "-p | -ezplayer  : définit s'il faut afficher ou non les informations"
    echo "        ^         relatives à EZplayer."
    echo "                  valeurs: true (defaut) | false"
    echo "-g | -global    : définit s'il faut afficher ou non les informations"
    echo "                  récoltées depuis la création d'EZcast."
    echo "                  valeurs: true (defaut) | false"
    echo "-i | -interval  : définit s'il faut afficher ou non les informations"
    echo "                  pour la période donnée"
    echo "                  valeurs: true (defaut) | false"
    echo "-d | -detail    : définit s'il faut afficher ou non la liste des cours"
    echo "                  et enseignants par type d'asset"
    echo "                  valeurs: true | false (defaut)"
    echo ""
    echo "Exemple d'utilisation:"
    echo "''''''''''''''''''''''"
    echo "./report_generator.sh -start 20150915 -end 20151231 -g false"
    echo ""
}

if [ "$#" -lt 4 ];then
    Print_help;
    exit;
fi

# Initialize our own variables:
min_date="unset";
max_date="unset";
ezmanager_info="true";
ezplayer_info="true";
global_info="true";
period_info="true";
detail="false";

while [ $# -gt 1 ]
do
key="$1"

case $key in
    -s|-start)
    min_date="$2"
    shift # past argument
    ;;
    -e|-end)
    max_date="$2"
    shift # past argument
    ;;
    -m|-ezmanager)
    ezmanager_info="$2"
    shift # past argument
    ;;
    -p|-ezplayer)
    ezplayer_info="$2"
    shift # past argument
    ;;
    -g|-global)
    global_info="$2"
    shift # past argument
    ;;
    -i|-interval)
    period_info="$2"
    shift # past argument
    ;;
    -d|-detail)
    detail="$2"
    shift # past argument
    ;;
esac
shift # past argument or value
done

if [ "$min_date" = "unset" ] || [ "$max_date" = "unset" ]; then
    Print_help;
    exit;
fi;

if [ $min_date -gt $max_date ]; then
    tmp=$min_date;
    min_date=$max_date;
    max_date=$tmp;
fi

# echo "min_date: $min_date"
# echo "max_date: $max_date"
# echo "ezmanager_info: $ezmanager_info"
# echo "ezplayer_info: $ezplayer_info"
# echo "global_info: $global_info"
# echo "period_info: $period_info"
# echo "detail: $detail"

formated_min_date=`echo $min_date | cut -c7-8 `-`echo $min_date | cut -c5-6`-`echo $min_date | cut -c0-4`;
formated_max_date=`echo $max_date | cut -c7-8 `-`echo $max_date | cut -c5-6`-`echo $max_date | cut -c0-4`;

echo "-----------------------------------------------------------------------";
echo "| Attention, l'exécution des scripts peut prendre beaucoup de temps ! |";
echo "-----------------------------------------------------------------------";
echo "";
echo "Rapport pour la période du $formated_min_date au $formated_max_date";
echo "";
if [ "$global_info" = "true" ]; then
    echo "***********************************************************************";
    echo "*              I N F O R M A T I O N S   G E N E R A L E S            *";
    echo "***********************************************************************";
    echo "";
    echo "Les informations suivantes ne tiennent pas compte des dates renseignées.";
    echo "Il s'agit d'un bilan depuis le début d'EZcast."
    echo "";
    if [ "$ezmanager_info" = "true" ]; then
        echo "== E Z M A N A G E R ==================================================";
        echo "";
        cd /var/lib/ezcast/repository
        ezmanager_total_users=`awk '/<author>/,/<\/author>/' */*/_metadata.xml |  sed "s/<author>/ /g" | sed "s/<\/author>/\n/g" | sed 's/^[ \t]*//' | grep -v "<title>" | sort | uniq | wc -l`
        echo "Nombre total d'utilisateurs différents (ayant soumis des vidéos | ";
        echo "et/ou ayant enregistré en auditoire) depuis le début d'EZcast   | " $ezmanager_total_users;
        echo "-----------------------------------------------------------------------";
        if [ "$detail" = "true" ]; then
        echo "liste des utilisateurs différents (ayant soumis des vidéos       ";
        echo "et/ou ayant enregistré en auditoire) depuis le début d'EZcast    ";
        echo "Le nombre représente le nombre de vidéos soumises ou             ";
        echo "enregistrées par l'utilisateur.                                  ";
        echo "-----------------------------------------------------------------------";
        awk '/<author>/,/<\/author>/' */*/_metadata.xml |  sed "s/<author>/ /g" | sed "s/<\/author>/\n/g" | sed 's/^[ \t]*//' | grep -v "<title>" | sort | uniq -c
        echo "-----------------------------------------------------------------------";
        fi
        ezmanager_total_submit_users=`awk '/<author>/,/<\/author>/' */*/_metadata.xml | grep "SUBMIT" | sed "s/<author>/ /g" | sed "s/<\/author>/\n/g" | sed 's/^[ \t]*//' | grep -v "<title>" | sort | uniq | wc -l`
        echo "Nombre total d'utilisateurs différents ayant soumis des vidéos  | ";
        echo "depuis le début d'EZcast                                        | " $ezmanager_total_submit_users;
        echo "-----------------------------------------------------------------------";
        if [ "$detail" = "true" ]; then
        echo "liste des utilisateurs différents ayant soumis des vidéos        ";
        echo "depuis le début d'EZcast                                         ";
        echo "Le nombre représente le nombre de vidéos soumises par            ";
        echo "l'utilisateur.                                                   ";
        echo "-----------------------------------------------------------------------";
        awk '/<author>/,/<\/author>/' */*/_metadata.xml | grep -i "SUBMIT" | sed "s/<author>/ /g" | sed "s/<\/author>/\n/g" | sed 's/^[ \t]*//' | grep -v "<title>" | sort | uniq -c
        echo "-----------------------------------------------------------------------";
        fi
        ezmanager_total_rec_users=`awk '/<author>/,/<\/author>/' */*/_metadata.xml | grep -iv "SUBMIT" | sed "s/<author>/ /g" | sed "s/<\/author>/\n/g" | sed 's/^[ \t]*//' | grep -v "<title>" | sort | uniq | wc -l`
        echo "Nombre total d'utilisateurs différents ayant enregistré en      | ";
        echo "auditoire depuis le début d'EZcast                              | " $ezmanager_total_rec_users;
        if [ "$detail" = "true" ]; then
        echo "-----------------------------------------------------------------------";
        echo "liste des utilisateurs différents ayant enregistré en auditoire  ";
        echo "depuis le début d'EZcast                                         ";
        echo "Le nombre représente le nombre de vidéos enregistrées.           ";
        echo "-----------------------------------------------------------------------";
        awk '/<author>/,/<\/author>/' */*/_metadata.xml | grep -iv "SUBMIT" | sed "s/<author>/ /g" | sed "s/<\/author>/\n/g" | sed 's/^[ \t]*//' | grep -v "<title>" | sort | uniq -c
        fi
        echo "=======================================================================";
        ezmanager_total_courses=`grep 'title' */*/_metadata.xml | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | cut -d '/' -f1 | sed -e 's/\-[^\-]*$//' | sort | uniq | wc -l`
        echo "Nombre total de cours différents (contenant des capsules et/ou  | ";
        echo "des enregistrements en auditoire) depuis le début d'EZcast      | " $ezmanager_total_courses;
        echo "-----------------------------------------------------------------------";
        if [ "$detail" = "true" ]; then
        echo "liste des cours différents (contenant des capsules et/ou des     ";
        echo "enregistrements en auditoire) depuis le début d'EZcast           ";
        echo "Le nombre représente le nombre de vidéos contenues dans l'album. ";
        echo "-----------------------------------------------------------------------";
        grep 'title' */*/_metadata.xml | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | cut -d '/' -f1 | sed -e 's/\-[^\-]*$//' | sort | uniq -c
        echo "-----------------------------------------------------------------------";
        fi
        ezmanager_total_submit_courses=`grep -l '<origin>SUBMIT' */*/_metadata.xml | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | cut -d '/' -f1 | sed -e 's/\-[^\-]*$//' | sort | uniq | wc -l`
        echo "Nombre total de cours différents contenant des capsules depuis  | ";
        echo "le début d'EZcast                                               | " $ezmanager_total_submit_courses;
        echo "-----------------------------------------------------------------------";
        if [ "$detail" = "true" ]; then
        echo "liste des cours différents contenant des capsules depuis le début ";
        echo "d'EZcast                                                         ";
        echo "Le nombre représente le nombre de vidéos contenues dans l'album. ";
        echo "-----------------------------------------------------------------------";
        grep -l '<origin>SUBMIT' */*/_metadata.xml | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | cut -d '/' -f1 | sed -e 's/\-[^\-]*$//' | sort | uniq -c
        echo "-----------------------------------------------------------------------";
        fi
        ezmanager_total_rec_courses=`grep 'title' */*/_metadata.xml | grep -v "SUBMIT" | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -v ‘awijns’ | cut -d '/' -f1 | sed -e 's/\-[^\-]*$//' | sort | uniq | wc -l`
        echo "Nombre total de cours différents contenant des enregistrements  | ";
        echo "faits en auditoire depuis le début d'EZcast                     | " $ezmanager_total_rec_courses;
        if [ "$detail" = "true" ]; then
        echo "-----------------------------------------------------------------------";
        echo "liste des cours différents contenant des enregistrements faits   ";
        echo "en auditoire depuis le début d'EZcast                            ";
        echo "Le nombre représente le nombre de vidéos contenues dans l'album. ";
        echo "-----------------------------------------------------------------------";
        grep 'title' */*/_metadata.xml | grep -v "SUBMIT" | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -v ‘awijns’ | cut -d '/' -f1 | sed -e 's/\-[^\-]*$//' | sort | uniq -c
        fi
        echo "=======================================================================";
        ezmanager_total_assets=`grep 'origin' */*/_metadata.xml | grep -v 'PODC' | grep -iv 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | wc -l`
        echo "Nombre total d'assets contenus dans le repository               | ";
        echo "(capsules + cours enregistrés depuis le début d'EZcast)         | ";
        echo "Ne tient pas compte des assets supprimés ni des tests           | " $ezmanager_total_assets;
        echo "-----------------------------------------------------------------------";
        ezmanager_total_submit_assets=`grep 'SUBMIT' */*/_metadata.xml | grep -v 'PODC' | grep -iv 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | wc -l`
        echo "Nombre total de capsules contenues dans le repository           | ";
        echo "Ne tient pas compte des assets supprimés ni des tests           | " $ezmanager_total_submit_assets;
        echo "-----------------------------------------------------------------------";
        ezmanager_total_rec_assets=`grep 'origin' */*/_metadata.xml | grep -iv 'SUBMIT' | grep -v 'PODC' | grep -iv 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | wc -l`
        echo "Nombre total de cours enregistrés contenus dans le repository   | ";
        echo "Ne tient pas compte des assets supprimés ni des tests           | " $ezmanager_total_rec_assets;
        echo "-----------------------------------------------------------------------";
        echo "";
    fi;
    if [ "$ezplayer_info" = "true" ]; then
        echo "";
        echo "== E Z P L A Y E R  ===================================================";
        cd /var/lib/ezcast/ezplayer
        ezplayer_total_users=`ls -la users/ | wc -l`
        echo "Nombre total d'utilisateurs différents depuis la création       | ";
        echo "d'EZplayer                                                      | " $ezplayer_total_users;
        echo "-----------------------------------------------------------------------";
        ezplayer_total_threads=`cat ezplayer_traces/????-??-??.trace | grep 'thread_add' | grep -iv 'TEST' | grep -v 'awijns' | grep -v 'PODC' | grep -iv 'TEST' | grep -iv 'DEMO' | wc -l`
        echo "Nombre total de discussions créées depuis la création          | ";
        echo "d'EZplayer (V2)                                                 | " $ezplayer_total_threads;
        echo "-----------------------------------------------------------------------";
        if [ "$detail" = "true" ]; then
            echo "Liste des cours contenant des discussions depuis la création";
            echo "d'EZplayer (V2)                                                 ";
            echo "Le nombre indique le nombre de discussions créées pour le cours. ";
            echo "-----------------------------------------------------------------------";
            cat ezplayer_traces/????-??-??.trace | grep 'thread_add' | grep -iv 'TEST' | grep -v 'awijns' | grep -v 'PODC' | grep -iv 'TEST' | grep -iv 'DEMO' | cut -d '|' -f 7 | sed -e 's/\-[^\-]*$//' | sort | uniq -c
            echo "-----------------------------------------------------------------------";
        fi;
        ezplayer_total_comments=`cat ezplayer_traces/????-??-??.trace | grep 'comment_add' | grep -iv 'TEST' | grep -v 'awijns' | grep -v 'PODC' | grep -iv 'TEST' | grep -iv 'DEMO' | wc -l`
        echo "Nombre total de commentaires ajoutés depuis la création         | ";
        echo "d'EZplayer (V2)                                                 | " $ezplayer_total_comments;
        echo "-----------------------------------------------------------------------";
        if [ "$detail" = "true" ]; then
            echo "Liste des cours contenant des commentaires depuis la création";
            echo "d'EZplayer (V2)                                                 ";
            echo "Le nombre indique le nombre de commentaires créés pour le cours. ";
            echo "-----------------------------------------------------------------------";
            cat ezplayer_traces/????-??-??.trace | grep 'comment_add' | grep -iv 'TEST' | grep -v 'awijns' | grep -v 'PODC' | grep -iv 'TEST' | grep -iv 'DEMO' | cut -d '|' -f 7 | sed -e 's/\-[^\-]*$//' | sort | uniq -c
            echo "-----------------------------------------------------------------------";
        fi;
        ezplayer_total_bookmarks=`cat ezplayer_traces/????-??-??.trace | grep 'bookmark_add' | grep -iv 'TEST' | grep -v 'awijns' | grep -v 'mjansens' | grep -v 'niroland' | grep -v 'PODC' | grep -iv 'TEST' | grep -iv 'DEMO' | wc -l`
        echo "Nombre total de signets (personnels et officiels) ajoutés       | ";
        echo "depuis la création d'EZplayer (traces)                          | " $ezplayer_total_bookmarks;
        echo "-----------------------------------------------------------------------";
        ezplayer_total_official_bookmarks=`cat ezplayer_traces/????-??-??.trace | grep 'official' | grep 'bookmark_add' | grep -iv 'TEST' | grep -v 'awijns' | grep -v 'mjansens' | grep -v 'niroland' | grep -v 'PODC' | grep -iv 'TEST' | grep -iv 'DEMO' | wc -l`
        echo "Nombre total de signets officiels ajoutés depuis la création    | ";
        echo "d'EZplayer (traces)                                             | " $ezplayer_total_official_bookmarks;
        echo "-----------------------------------------------------------------------";
        ezplayer_total_personal_bookmarks=`cat ezplayer_traces/????-??-??.trace | grep -v 'official' | grep 'bookmark_add' | grep -iv 'TEST' | grep -v 'awijns' | grep -v 'mjansens' | grep -v 'niroland' | grep -v 'PODC' | grep -iv 'TEST' | grep -iv 'DEMO' | wc -l`
        echo "Nombre total de signets personnels ajoutés depuis la création   | ";
        echo "d'EZplayer (traces)                                             | " $ezplayer_total_personal_bookmarks;
        echo "-----------------------------------------------------------------------";
        ezplayer_total_official_bookmarks_users=`cat ezplayer_traces/????-??-??.trace | grep 'bookmark_add' | grep -iv 'TEST' | grep -v 'awijns' | grep -v 'niroland' | grep -v 'mjansens' | grep -v 'PODC' | grep -iv 'TEST' | grep -iv 'DEMO' | grep -i 'official' | cut -d '|' -f 4 | sort | uniq | wc -l`
        echo "Nombre total d'utilisateurs différents ayant ajouté des signets | ";
        echo "officiels depuis la création d'EZplayer d'EZplayer (traces)     | " $ezplayer_total_official_bookmarks_users;
        echo "-----------------------------------------------------------------------";
        ezplayer_total_personal_bookmarks_users=`cat ezplayer_traces/????-??-??.trace | grep 'bookmark_add' | grep -iv 'TEST' | grep -v 'awijns' | grep -v 'niroland' | grep -v 'mjansens' | grep -v 'PODC' | grep -iv 'TEST' | grep -iv 'DEMO' | grep -iv 'official' | cut -d '|' -f 4 | sort | uniq | wc -l`
        echo "Nombre total d'utilisateurs différents ayant ajouté des signets | ";
        echo "personnels depuis la création d'EZplayer d'EZplayer (traces)    | " $ezplayer_total_personal_bookmarks_users;
        echo "-----------------------------------------------------------------------";
        echo "";
        echo "";
    fi;
fi;

if [ "$period_info" = "true" ]; then
echo "***********************************************************************";
echo "*      I N F O R M A T I O N S   P O U R   L A   P E R I O D E        *";
echo "***********************************************************************";
echo "";
echo "Les informations suivantes concernent la période choisie. Cette période";
echo "commence au $formated_min_date et s'achève au $formated_max_date       ";
echo "";
if [ "$ezmanager_info" = "true" ]; then
echo "== E Z M A N A G E R ==================================================";
# prepares a global metadata file that contains information only for the 
# assets that are in our range of dates
cd /var/lib/ezcast/repository
report_tmp_path="/tmp/report_ezmanager_${min_date}_${max_date}"
# loops on all private albums of the repository
for album in *-priv; do
    cd $album;
    # loops on all assets of each album
    for asset in ????_??_??_??h??; do
        if [ "$asset" != "????_??_??_??h??" ]; then
            # transforms asset : 2015_09_15_00h00 --> 20150915
            formated_asset=`echo "$asset" | sed 's/\_//g'`
            formated_asset=`echo $formated_asset | cut -c0-8` 
            # verifies that the date of the asset is greater or equal to min_date
            # and lower or equal to max_date
            if [ $formated_asset -ge $min_date ] && [ $formated_asset -le $max_date ]; then
                echo `printf "<album>$album</album>" && cat $asset/_metadata.xml | grep "origin"` >> $report_tmp_path
            fi
        fi
    done;
    cd ..
done
cd /var/lib/ezcast/repository
# loops on all public albums of the repository
for album in *-pub; do
    cd $album;
    # loops on all assets of each album
    for asset in ????_??_??_??h??; do
        if [ "$asset" != "????_??_??_??h??" ]; then
            # transforms asset : 2015_09_15_00h00 --> 20150915
            formated_asset=`echo "$asset" | sed 's/\_//g'`
            formated_asset=`echo $formated_asset | cut -c0-8` 
            # verifies that the date of the asset is greater or equal to min_date
            # and lower or equal to max_date
            if [ $formated_asset -ge $min_date ] && [ $formated_asset -le $max_date ]; then
                echo `printf "<album>$album</album>" && cat $asset/_metadata.xml | grep "origin"` >> $report_tmp_path
            fi
        fi
    done;
    cd ..
done;

ezmanager_period_users=`sed -n '/author/{s/.*<author>//;s/<\/author.*//;p;}' $report_tmp_path | sort | uniq | wc -l`
echo "Nombre d'utilisateurs différents (ayant soumis des vidéos et/ou | ";
echo "ayant enregistré en auditoire) pour la période donnée           | " $ezmanager_period_users;
echo "-----------------------------------------------------------------------";
echo "Liste des utilisateurs pour la période donnée"
echo "-----------------------------------------------------------------------";
sed -n '/author/{s/.*<author>//;s/<\/author.*//;p;}' $report_tmp_path | sort | uniq
echo "-----------------------------------------------------------------------";
ezmanager_period_submit_users=`cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -i "SUBMIT" | sed -n '/author/{s/.*<author>//;s/<\/author.*//;p;}' | sort | uniq | wc -l`
echo "Nombre d'utilisateurs différents ayant soumis des vidéos pour   | ";
echo "la période donnée                                               | " $ezmanager_period_submit_users;
echo "-----------------------------------------------------------------------";
if [ "$detail" = "true" ]  && [ $ezmanager_period_submit_users -gt 0 ]; then
echo "Liste des utilisateurs ayant soumis des vidéos pour la période donnée."
echo "Le nombre indique le nombre de vidéos soumises.                       "
echo "-----------------------------------------------------------------------";
cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -i "SUBMIT" | sed -n '/author/{s/.*<author>//;s/<\/author.*//;p;}' | sort | uniq -c
echo "-----------------------------------------------------------------------";
fi;
ezmanager_period_rec_users=`cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -iv "SUBMIT" | sed -n '/author/{s/.*<author>//;s/<\/author.*//;p;}' | sort | uniq | wc -l`
echo "Nombre d'utilisateurs différents ayant enregistré en auditoire  | ";
echo "pour la période donnée                                          | " $ezmanager_period_rec_users;
if [ "$detail" = "true" ] && [ $ezmanager_period_rec_users -gt 0 ]; then
echo "-----------------------------------------------------------------------";
echo "Liste des utilisateurs ayant enregistré en auditoire pour la période donnée."
echo "Le nombre indique le nombre de vidéos enregistrées.                       "
cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -iv "SUBMIT" | sed -n '/author/{s/.*<author>//;s/<\/author.*//;p;}' | sort | uniq -c
fi;
echo "=======================================================================";
ezmanager_period_courses=`cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | awk -F'>' '{print $2}' | awk -F'<' '{print $1}' | sed -e 's/\-[^\-]*$//' | sort | uniq | wc -l`
echo "Nombre de cours différents (contenant des capsules et/ou        | ";
echo "des enregistrements en auditoire) pour la période donnée        | " $ezmanager_period_courses;
echo "-----------------------------------------------------------------------";
echo "Ventilation des assets soumis ou enregistrés par cours"
echo "-----------------------------------------------------------------------";
cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | awk -F'>' '{print $2}' | awk -F'<' '{print $1}' | sed -e 's/\-[^\-]*$//' | sort | uniq -c
echo "-----------------------------------------------------------------------";
ezmanager_period_submit_courses=`cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -i "SUBMIT" | awk -F'>' '{print $2}' | awk -F'<' '{print $1}' | sed -e 's/\-[^\-]*$//' | sort | uniq | wc -l`
echo "Nombre de cours différents contenant des capsules pour la       | ";
echo "période donnée                                                  | " $ezmanager_period_submit_courses;
echo "-----------------------------------------------------------------------";
if [ "$detail" = "true" ] && [ $ezmanager_period_submit_courses -gt 0 ]; then
echo "Liste des cours dans lesquelles au moins une vidéo a été soumise "
echo "durant la période donnée.                                                   " 
echo "Le nombre indique le nombre de vidéos soumises.               "
echo "-----------------------------------------------------------------------";
cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -i "SUBMIT" | awk -F'>' '{print $2}' | awk -F'<' '{print $1}' | sed -e 's/\-[^\-]*$//' | sort | uniq -c
echo "-----------------------------------------------------------------------";
fi;
ezmanager_period_rec_courses=`cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -iv "SUBMIT" | awk -F'>' '{print $2}' | awk -F'<' '{print $1}' | sed -e 's/\-[^\-]*$//' | sort | uniq | wc -l`
echo "Nombre de cours différents contenant des enregistrements        | ";
echo "faits en auditoire pour la période donnée                       | " $ezmanager_period_rec_courses;
if [ "$detail" = "true" ] && [ $ezmanager_period_rec_courses -gt 0 ]; then
echo "-----------------------------------------------------------------------";
echo "Liste des cours dans lesquelles au moins une vidéo a enregistrée "
echo "durant la période donnée.                                                   " 
echo "Le nombre indique le nombre de vidéos soumises.               "
cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -iv "SUBMIT" | awk -F'>' '{print $2}' | awk -F'<' '{print $1}' | sed -e 's/\-[^\-]*$//' | sort | uniq -c
fi;
echo "=======================================================================";
ezmanager_period_assets=`cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | wc -l`
echo "Nombre d'assets ajoutés au repository pour la période donnée    | ";
echo "(capsules + cours enregistrés)         | ";
echo "Ne tient pas compte des assets supprimés ni des tests           | " $ezmanager_period_assets;
echo "-----------------------------------------------------------------------";
ezmanager_period_submit_assets=`cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -i "SUBMIT" | wc -l`
echo "Nombre de capsules soumises dans le repository pour la période  | ";
echo "Ne tient pas compte des assets supprimés ni des tests           | " $ezmanager_period_submit_assets;
echo "-----------------------------------------------------------------------";
ezmanager_period_rec_assets=`cat $report_tmp_path | grep -v 'PODC' | grep -v 'TEST' | grep -v 'DEMO' | grep -v 'APR-POD' | grep -iv "SUBMIT" | wc -l`
echo "Nombre de cours enregistrés ajoutés au repository pour la       | ";
echo "période donnée                                                  | ";
echo "Ne tient pas compte des assets supprimés ni des tests           | " $ezmanager_period_rec_assets;
echo "-----------------------------------------------------------------------";
echo "Utilisation des auditoires";
php get_duration.php $min_date $max_date;

unlink $report_tmp_path
fi;

if [ "$ezplayer_info" = "true" ]; then
echo "== E Z P L A Y E R ====================================================";
# prepares a global trace file that contains only the traces that 
# are in our range of dates
cd /var/lib/ezcast/ezplayer/ezplayer_traces
ezplayer_report_tmp_path="/tmp/report_ezplayer_${min_date}_${max_date}"

# loops on all traces
for trace in ????-??-??.trace; do
    if [ "$trace" != "????-??-??.trace" ]; then
        # transforms trace : 2015-09-15.trace --> 20150915
        formated_trace=`echo "$trace" | sed 's/\-//g' | cut -c0-8`
        # verifies that the date of the trace is greater or equal to min_date
        # and lower or equal to max_date
        if [ $formated_trace -ge $min_date ] && [ $formated_trace -le $max_date ]; then
            cat $trace >> $ezplayer_report_tmp_path
        fi
    fi
done;

cd /var/lib/ezcast/ezplayer

ezplayer_period_users=`cat $ezplayer_report_tmp_path | grep ' login' | cut -d '|' -f4 | sort | uniq | wc -l`
echo "Nombre d'utilisateurs (authentifiés) différents pour la période | ";
echo "donnée                                                          | " $ezplayer_period_users;
echo "-----------------------------------------------------------------------";
# gets different ip's of all authenticated users
cat $ezplayer_report_tmp_path | grep '^20' | grep -v "nologin" | cut -d '|' -f 3 | sort | uniq >> /tmp/users_ip
# gets different ip's of all anonymous users
cat $ezplayer_report_tmp_path | grep "nologin" | cut -d '|' -f 3 | sort | uniq >> /tmp/nologin_ip
# keeps ip's that are in anonymous ip's list but not in authenticated users' ip's
ezplayer_period_anon_users=`comm -1 -3 /tmp/users_ip /tmp/nologin_ip | wc -l`
echo "Nombre d'utilisateurs (anonymes) pour la période donnée         | ";
echo "Approximation basée sur les adresses ip différentes             | " $ezplayer_period_anon_users;
echo "-----------------------------------------------------------------------";
unlink /tmp/nologin_ip
unlink /tmp/users_ip
echo "Classement des navigateurs web | OS par ordre d'utilisation      " 
echo "pour la période donnée" 
echo "-----------------------------------------------------------------------";
cat $ezplayer_report_tmp_path | grep ' login' | cut -d '|' -f7,9 | sort | uniq -c | sort -r
echo "-----------------------------------------------------------------------";
ezplayer_period_albums=`cat $ezplayer_report_tmp_path |  grep 'view_album_assets' | cut -d '|' -f 7 | sort | uniq | wc -l`
echo "Nombre d'albums différents consultés pour la période donnée     | ";
echo "(parcourir l'album, sans forcément cliquer sur un asset)        | ";
echo "Action: view_album_assets (voir desc. actions)                  | " $ezplayer_period_albums;
echo "-----------------------------------------------------------------------";
if [ "$detail" = "true" ]; then
ezplayer_period_albums=`cat $ezplayer_report_tmp_path |  grep 'view_album_assets' | cut -d '|' -f 7 | sort | uniq | wc -l`
echo "Liste des albums consultés pour la période donnée, classée par nombre ";
echo "de consultations (action de parcourir l'album)";
echo "-----------------------------------------------------------------------";
cat $ezplayer_report_tmp_path |  grep 'view_album_assets' | cut -d '|' -f 7 | sort | uniq -c | sort -r
echo "-----------------------------------------------------------------------";
else 
echo "Top 10 des albums les plus consultés pour la période donnée. ";
echo "(action de parcourir l'album)";
echo "-----------------------------------------------------------------------";
cat $ezplayer_report_tmp_path |  grep 'view_album_assets' | cut -d '|' -f 7 | sort | uniq -c | sort -r | head -10
echo "-----------------------------------------------------------------------";
fi
ezplayer_period_albums_view=`cat $ezplayer_report_tmp_path |  grep 'view_asset_detail' | cut -d '|' -f 7 | sort | uniq | wc -l`
echo "Nombre d'albums différents contenant au moins un asset ayant    | " ;
echo "été consulté pour la période donnée.                            | " ;
echo "contrairement aux albums consultés, qui peuvent avoir été       | " ;
echo "parcourus sans avoir cliqué sur aucune vidéo, ce nombre-ci      | " ;
echo "ne tient compte que des albums dans lesquels au moins un        | " ;
echo "utilisateur a consulté au moins un asset.                       | " ;
echo "Action: view_asset_details (voir desc. actions)                 | " $ezplayer_period_albums_view;
echo "-----------------------------------------------------------------------";
echo "Top 10 des albums pour lesquels au moins un asset a été       ";
echo "consulté par un utilisateur pour la période donnée.           ";
echo "Le nombre indique le nombre d'assets consultés dans l'album   ";
echo "-----------------------------------------------------------------------";
cat $ezplayer_report_tmp_path |  grep 'view_asset_detail' | cut -d '|' -f 7 | sort | uniq -c | sort -r | head -10
echo "-----------------------------------------------------------------------";
ezplayer_period_assets=`cat $ezplayer_report_tmp_path |  grep 'view_asset_detail' | cut -d '|' -f 7,8 | sort | uniq | wc -l`
echo "Nombre d'assets différents consultés pour la période donnée     | " ;
echo "Action: view_asset_details (voir desc. actions)                 | " $ezplayer_period_assets;
echo "-----------------------------------------------------------------------";
ezplayer_period_assets_total=`cat $ezplayer_report_tmp_path |  grep 'view_asset_detail' | wc -l`
echo "Nombre de consultations d'asset pour la période donnée          | " 
echo "(Un même asset peut avoir été consulté plusieurs fois)          | " $ezplayer_period_assets_total;
echo "-----------------------------------------------------------------------";
echo "Top 10 des assets les plus consultés       ";
echo "Le nombre indique le nombre de fois que l'asset a été consulté  ";
echo "-----------------------------------------------------------------------";
cat $ezplayer_report_tmp_path |  grep 'view_asset_detail' | cut -d '|' -f 7,8 | sort | uniq -c | sort -r | head -10
echo "-----------------------------------------------------------------------";
ezplayer_period_threads=`cat $ezplayer_report_tmp_path | grep 'thread_add' | grep -iv 'test' | grep -iv 'demo' | grep -iv 'podc' | wc -l`
echo "Nombre de discussions crées pour la période donnée              | " ;
echo "Action: thread_add (voir desc. actions)                         | " $ezplayer_period_threads;
echo "-----------------------------------------------------------------------";
echo "Top 10 des cours dans lesquels le plus de discussions ont été créées.";
echo "Le nombre indique le nombre de discussions créées pour la période donnée  ";
echo "-----------------------------------------------------------------------";
cat $ezplayer_report_tmp_path |  grep 'thread_add' | cut -d '|' -f 7 | sort | uniq -c | sort -r | head -10
echo "-----------------------------------------------------------------------";
ezplayer_period_comments=`cat $ezplayer_report_tmp_path | grep 'comment_add' | grep -iv 'test' | grep -iv 'demo' | grep -iv 'podc' | wc -l`
echo "Nombre de commentaires ajoutés pour la période donnée            | " ;
echo "Action: comment_add (voir desc. actions)                         | " $ezplayer_period_comments;
echo "-----------------------------------------------------------------------";
ezplayer_period_personal_bookmarks=`cat $ezplayer_report_tmp_path | grep 'bookmark_add' | grep -v 'official' | grep -iv 'test' | grep -iv 'demo' | grep -iv 'podc' | wc -l`
echo "Nombre de signets personnels créés pour la période donnée        | " ;
echo "Action: bookmark_add (voir desc. actions)                        | " $ezplayer_period_personal_bookmarks;
echo "-----------------------------------------------------------------------";
ezplayer_period_personal_users=`cat $ezplayer_report_tmp_path | grep 'bookmark_add' | grep -v 'official' | grep -iv 'test' | grep -iv 'demo' | grep -iv 'podc' | cut -d '|' -f 4 | sort | uniq | wc -l`
echo "Nombre d'utilisateurs différents ayant créé des signets          | " ;
echo "personnels pour la période donnée                                | " ;
echo "Action: bookmark_add (voir desc. actions)                        | " $ezplayer_period_personal_users;
echo "-----------------------------------------------------------------------";
echo "Top 10 des cours dans lesquels le plus de signets (officiels et personnels)"; 
echo "ont été ajoutés. ";
echo "Le nombre indique le nombre de signets ajoutés pour la période donnée  ";
echo "-----------------------------------------------------------------------";
cat $ezplayer_report_tmp_path |  grep 'bookmark_add' | cut -d '|' -f 7 | sort | uniq -c | sort -r | head -10
echo "-----------------------------------------------------------------------";
ezplayer_period_official_bookmarks=`cat $ezplayer_report_tmp_path | grep 'bookmark_add' | grep 'official' | grep -iv 'test' | grep -iv 'demo' | grep -iv 'podc' | wc -l`
echo "Nombre de signets officiels créés pour la période donnée         | " ;
echo "Action: bookmark_add (voir desc. actions)                        | " $ezplayer_period_official_bookmarks;
echo "-----------------------------------------------------------------------";
ezplayer_period_official_users=`cat $ezplayer_report_tmp_path | grep 'bookmark_add' | grep 'official' | grep -iv 'test' | grep -iv 'demo' | grep -iv 'podc' | cut -d '|' -f 4 | sort | uniq | wc -l`
echo "Nombre d'utilisateurs différents ayant créé des signets officiels| " ;
echo "pour la période donnée                                           | " ;
echo "Action: bookmark_add (voir desc. actions)                        | " $ezplayer_period_official_users;
echo "-----------------------------------------------------------------------";
echo "Top 10 des utilisateurs ajoutant le plus de signets officiels.";
echo "Le nombre indique le nombre de signets ajoutés pour la période donnée  ";
echo "-----------------------------------------------------------------------";
cat $ezplayer_report_tmp_path |  grep 'bookmark_add' | grep -i 'official' | grep -iv 'test' | grep -iv 'demo' | grep -iv 'podc' | cut -d '|' -f 4 | sort | uniq -c | sort -r | head -10
echo "-----------------------------------------------------------------------";

unlink $ezplayer_report_tmp_path
fi;
fi;

echo "";
echo "";


