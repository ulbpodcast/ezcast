<?php

/*
* EZCAST EZadmin
* Copyright (C) 2016 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
*                   Thibaut Roskam
*
* This software is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 3 of the License, or (at your option) any later version.
*
* This software is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this software; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
 * This script is called every night to count the number of albums in ezmanager and update the DB accordingly
 */

require_once 'config.inc';
require_once '../commons/lib_database.php';

$do_users=true;
$do_courses=true;
$do_users_courses=true;
$do_sync=true;
$do_users=false;
$do_courses=false;
$anac='201718';
//$do_users_courses=FALSE;
//$do_sync=false;

db_prepare();

//========================================================================
//USERS
if ($do_users) {
    if (1==1) {
        $query="SELECT DISTINCT(netID) as netID,NOM,PRENOM 
FROM `ban_digger_VW_LISTE_ENSEIGNANTS`, `dcor_digger_matricules_actifs_ulb` 
WHERE ban_digger_VW_LISTE_ENSEIGNANTS.MATRICULE != '' 
  AND TERM='$anac' 
  AND netID!='NO'
  AND netID!='VO'
  AND ban_digger_VW_LISTE_ENSEIGNANTS.MATRICULE=dcor_digger_matricules_actifs_ulb.matricule 
ORDER BY netID";

        $sth=$db_object->prepare($query);
        $sth->execute();

        $ban_users =$sth->fetchall(PDO::FETCH_ASSOC);

        //var_dump($ban_users);
        //die;
        $sth->closeCursor();
        $query = "SELECT `user_ID`,`surname`, `forename`,`origin` FROM `ezcast_users` ORDER BY `user_ID`";
        $sth2 = $db_object->prepare($query);
        $sth2->execute();
        //echo "\nPDOStatement::errorInfo():\n";
        //$arr = $sth2->errorInfo();
        //print_r($arr);
        $ez_users = $sth2->fetchall(PDO::FETCH_ASSOC);
        //var_dump($ez_users);
        $sth2->closeCursor();

        //file_put_contents("ban_users.json", json_encode($ban_users, $options));
//file_put_contents("ez_users.json", json_encode($ez_users, $options));
//die;
    } else {
        $ban_users = json_decode(file_get_contents("ban_users.json"), true);
        $ez_users = json_decode(file_get_contents("ez_users.json"), true);
    }
    $sql_users_cmd_array = ban_ez_users_diff($ban_users, $ez_users);
    $sql_users_cmd=implode("\n", $sql_users_cmd_array);
    //var_dump($sql_cmd_array);
    print "\n-- =======================================\n";
    print "--                 USERS                  \n";
    print "-- ========================================\n";
    print $sql_users_cmd;
}
//========================================================================
//COURSES
if ($do_courses) {
    $query="SELECT  DISTINCT(MNEMONIQUE) AS MNEMONIQUE, TERM,INTITULE FROM `ban_digger_VW_LISTE_COURS` WHERE TERM='$anac' AND MNEMONIQUE!='' AND MNEMONIQUE NOT LIKE '%-Z-%' ORDER BY MNEMONIQUE";
    $sth = $db_object->prepare($query);
    $sth->execute();
    $ban_courses = $sth->fetchall(PDO::FETCH_ASSOC);
    $sth->closeCursor();
    $nb_ban_courses=count($ban_courses);
    print "SELECT BAN COURSES DONE: {$nb_ban_courses} records\n";
    $query="SELECT course_code,course_name,origin FROM `ezcast_courses` ORDER BY course_code";
    $sth2 = $db_object->prepare($query);
    $sth2->execute();
    $ez_courses = $sth2->fetchall(PDO::FETCH_ASSOC);
    $sth2->closeCursor();
    $nb_ez_courses=count($ez_courses);
    print "SELECT EZ COURSES DONE {$nb_ez_courses} records\n";
    file_put_contents("ban_courses.json", json_encode($ban_courses));
    file_put_contents("ez_courses.json", json_encode($ez_courses));
    $sql_courses_cmd_array = ban_ez_courses_diff($ban_courses, $ez_courses);
    $sql_courses_cmd=implode("\n", $sql_courses_cmd_array);
    //var_dump($sql_cmd_array);
    print "-- =======================================";
    print "--                COURSES                  ";
    print "-- =======================================";
    print $sql_courses_cmd."\n";
    //var_dump($ban_users);
    //die;
    $sth->closeCursor();
}

//========================================================================
//USERS COURSES
if ($do_users_courses) {
    $query="SELECT netID,MNEMONIQUE 
FROM `ban_digger_VW_LISTE_ENSEIGNANTS`, `dcor_digger_matricules_actifs_ulb` 
WHERE ban_digger_VW_LISTE_ENSEIGNANTS.MATRICULE != '' 
  AND TERM='$anac' 
  AND netID!='NO'
  AND netID!='VO'
  AND MNEMONIQUE!=''
  AND MNEMONIQUE NOT LIKE '%-Z-%'
  AND ban_digger_VW_LISTE_ENSEIGNANTS.MATRICULE=dcor_digger_matricules_actifs_ulb.matricule 
ORDER BY MNEMONIQUE,netID";

    $sth=$db_object->prepare($query);
    $sth->execute();

    $ban_users_courses =$sth->fetchall(PDO::FETCH_ASSOC);
    //var_dump($ban_users_courses);
    $sth->closeCursor();

    $query = "SELECT `user_ID`,`course_code`,`origin` FROM `ezcast_users_courses` ORDER BY `course_code`,`user_ID`";
    $sth2 = $db_object->prepare($query);
    $sth2->execute();
    //echo "\nPDOStatement::errorInfo():\n";
    //$arr = $sth2->errorInfo();
    //print_r($arr);
    $ez_users_courses = $sth2->fetchall(PDO::FETCH_ASSOC);
    //var_dump($ez_users);
    $sth2->closeCursor();
    file_put_contents("ban_users_courses.json", json_encode($ban_users_courses));
    file_put_contents("ez_users_courses.json", json_encode($ez_users_courses));
    //}
    //else{
    //$ban_users_courses = json_decode(file_get_contents("ban_users_courses.json"),true);
    //$ez_users_courses = json_decode(file_get_contents("ez_users_courses.json"),true);
    //}
    $sql_users_courses_cmd_array = ban_ez_users_courses_diff($ban_users_courses, $ez_users_courses);
    $sql_users_courses_cmd=implode("\n", $sql_users_courses_cmd_array);
    //var_dump($sql_cmd_array);
    print "-- =======================================";
    print "--          USERS COURSES                  ";
    print "-- =======================================";
    print $sql_users_courses_cmd;
}


//NOW DO THE BIG SYNC
if ($do_sync) {
    print "\nsyncing users...";
    foreach ($sql_users_cmd_array as $sql_users_cmd_line) {
        if (trim($sql_users_cmd_line)!="" && substr($sql_users_cmd_line, 0, 2)!="--") {
            print "cmd:". $sql_users_cmd_line."\n";
            $sth = $db_object->prepare($sql_users_cmd_line);
            if (!$sth) {
                print "error:";
                var_dump($sth->errorInfo());
            }
            $res=$sth->execute();
            if (!$res) {
                print "error:";
                var_dump($sth->errorInfo());
            }
            $sth->closeCursor();
        }//endif not empty or not --
    }
    print "\n";
        
    print "\nsyncing courses...";
    foreach ($sql_courses_cmd_array as $sql_courses_cmd_line) {
        if (trim($sql_courses_cmd_line)!="" && substr($sql_courses_cmd_line, 0, 2)!="--") {
            print "cmd:". $sql_courses_cmd_line."\n";
            $sth = $db_object->prepare($sql_courses_cmd_line);
            if (!$sth) {
                print "error:";
                var_dump($sth->errorInfo());
            }
            $sth->execute();
            if (!$res) {
                print "error:";
                var_dump($sth->errorInfo());
            }
        }
    }
    print "\n";
        
    print "\nsyncing users_courses...";
    foreach ($sql_users_courses_cmd_array as $sql_users_courses_cmd_line) {
        if (trim($sql_users_courses_cmd_line)!="" && substr($sql_users_courses_cmd_line, 0, 2)!="--") {
            print "cmd:". $sql_users_courses_cmd_line."\n";
            $sth = $db_object->prepare($sql_users_courses_cmd_line);
            if (!$sth) {
                print "error:";
                var_dump($sth->errorInfo());
            }
            $sth->execute();
            if (!$res) {
                print "error:";
                var_dump($sth->errorInfo());
            }
        }
    }
    print "\n";
}
  
    




/*
//TEST MNEMONIQUE between ban_digger_VW_LISTE_ENSEIGNANTS AND
if( false ){
$query="SELECT  DISTINCT(MNEMONIQUE) AS MNEMONIQUE FROM `ban_digger_VW_LISTE_COURS` WHERE TERM='$anac' AND MNEMONIQUE!='' ORDER BY MNEMONIQUE";
 $sth=$db_object->prepare($query);
 $sth->execute();

 $ban_courses =$sth->fetchall( PDO::FETCH_ASSOC);

var_dump($ban_courses);
//die;
$sth->closeCursor();
$query="SELECT DISTINCT(MNEMONIQUE) AS MNEMONIQUE
FROM `ban_digger_VW_LISTE_ENSEIGNANTS`
WHERE ban_digger_VW_LISTE_ENSEIGNANTS.MATRICULE != ''
  AND TERM='$anac'
  AND MNEMONIQUE!=''
ORDER BY MNEMONIQUE";
$sth2 = $db_object->prepare($query);
$sth2->execute();
//echo "\nPDOStatement::errorInfo():\n";
//$arr = $sth2->errorInfo();
//print_r($arr);
$ban_users = $sth2->fetchall( PDO::FETCH_ASSOC);
file_put_contents("ban_users.json", json_encode($ban_users));
file_put_contents("ban_courses.json", json_encode($ban_courses));
var_dump($ban_users);
$sth2->closeCursor();
}
else{
$ban_users = json_decode(file_get_contents("ban_users.json"),true);
$ban_courses = json_decode(file_get_contents("ban_courses.json"),true);
}


$sql_cmd_array = ban_ban_users_courses_diff($ban_courses,$ban_users);
//var_dump($sql_cmd_array);
print "=======================================";
print "         TEST BAN ENSEIGNANTS-COURS COHERENCE TO SHOW THAT SOME TEACHERS ARE GIVING UNKNOWN COURSES";
print "=======================================";
print implode("\n", $sql_cmd_array);

//}
*/
/*
 * DELETE DUPLICATE USERCOURSES:
 DELETE FROM ezcast_users_courses WHERE ID IN
(SELECT x.ID as ID FROM
(SELECT uc2.ID FROM `ezcast_users_courses` as uc1, `ezcast_users_courses` as uc2
WHERE
uc1.`course_code` = uc2.`course_code` AND
uc1.`user_ID` = uc2.`user_ID` AND
uc1.`ID` < uc2.`ID`) x)
 */
db_close();


    

function ban_ez_users_diff($ban_users, $ez_users)
{
    next1($ban_users, true);
    next2($ez_users, true);
    return diff('next1', 'next2', 'ban_ez_users_cmp', 'ez_user_insert', 'ez_user_delete', 'ez_user_update');
}

function ban_ez_courses_diff($ban_courses, $ez_courses)
{
    next1($ban_courses, true);
    next2($ez_courses, true);
    return diff('next1', 'next2', 'ban_ez_courses_cmp', 'ez_course_insert', 'ez_course_delete', 'ez_course_update');
}

function ban_ez_users_courses_diff($ban_users_courses, $ez_users_courses)
{
    next1($ban_users_courses, true);
    next2($ez_users_courses, true);
    return diff('next1', 'next2', 'ban_ez_users_courses_cmp', 'ez_users_courses_insert', 'ez_users_courses_delete', 'ez_users_courses_update');
}

function ban_ban_users_courses_diff($ban_courses, $ban_users)
{
    next1($ban_courses, true);
    next2($ban_users, true);
    return diff('next1', 'next2', 'ban_ban_users_courses_cmp', 'ban_users_courses_insert', 'ban_users_courses_delete', 'ban_users_courses_update');
}

//==========================================================================================
//USERS comparison functions (needed to do a diff)
function ban_ez_users_cmp($ban, $ez)
{
    $result=array();
    if (strtolower($ban['netID'])==strtolower($ez['user_ID'])) {
        $result['key']=0; //same keys
        if (strtolower($ban['NOM'])==strtolower($ez['surname']) && strtolower($ban['PRENOM'])==strtolower($ez['forename']) && $ez['origin']=="external" && strtoupper($ez['surname'])!=$ez['surname']) {
            $result['data']=0;
        } //same data
        else {
            $result['data']=1;
        } //not same data or uppercase name in ezcast or origin internal to become external
    } elseif (strtolower($ban['netID']) > strtolower($ez['user_ID'])) {
        $result['key']=1; //ban netid greater
    } else {
        $result['key']=2; //ban netid smaller
    }
    return $result;
}

function ez_user_insert($ban)
{
    global $db_object;
    //capitalise each word of the name BANNER SEEMS TO DO A TOUPPER ON **MOST** NAMES
    $nom=ucwords(strtolower($ban['NOM']));
    //capitalise each word of the name
    $sql="INSERT INTO ezcast_users (`user_ID`,`surname`, `forename`,`origin`) VALUES ('{$ban['netID']}',{$db_object->quote($nom)},{$db_object->quote($ban['PRENOM'])},'external'); ";
    return $sql;
}
function ez_user_delete($ez)
{
    if ($ez['origin']=="external") {
        $sql="DELETE FROM ezcast_users WHERE user_ID='{$ez['user_ID']}';";
    } else {
        $sql="";
    }//keep internal users
    return $sql;
}
function ez_user_update($ban)
{
    global $db_object;
    $nom_quoted=$db_object->quote(ucwords(strtolower($ban['NOM'])));
    $sql="UPDATE ezcast_users SET  `surname`={$nom_quoted} , `forename` = {$db_object->quote($ban['PRENOM'])} , `origin`='external' WHERE user_ID='{$ban['netID']}';";
    return $sql;
}
//==========================================================================================
//COURSES comparison functions (needed to do a diff)
 function ban_ez_courses_cmp($ban, $ez)
 {
     $result=array();
     /* print "\ncomparing:\n";
      print_r($ban);
      print "\n-------\n";
      print_r($ez);
      print "\n============\n"; */
     if (strtolower($ban['MNEMONIQUE'])==strtolower($ez['course_code'])) {
         $result['key']=0; //same keys
         if ($ban['INTITULE']==$ez['course_name'] && $ez['origin']=="external") {
             $result['data']=0; //same data
        //print "same data\n";
         } else {
             $result['data']=1; // not same data
        //print "data differ\n";
         }
     } elseif (strtolower($ban['MNEMONIQUE']) > strtolower($ez['course_code'])) {
         $result['key']=1; //ban MNEMONIQUE greater
          //  print "key1 > key2\n";
     } else {
         $result['key']=2; //ban MNEMONIQUE smaller
           // print "key1 < key2\n";
     }
     return $result;
 }

function ez_course_insert($ban)
{
    global $db_object;
    $sql="INSERT INTO ezcast_courses (`course_code`,`course_name`,`origin`) VALUES ('{$ban['MNEMONIQUE']}',{$db_object->quote($ban['INTITULE'])},'external'); ";
    return $sql;
}
function ez_course_delete($ez)
{
    if ($ez['origin']=="external") {
        $sql="DELETE FROM ezcast_courses WHERE course_code='{$ez['course_code']}';";
    } else {
        $sql="";
    }//keep internal courses
    return $sql;
}
 function ez_course_update($ban)
 {
     global $db_object;
     $sql="UPDATE ezcast_courses SET  `course_name`={$db_object->quote($ban['INTITULE'])}, `origin`='external' WHERE course_code='{$ban['MNEMONIQUE']}';";
     return $sql;
 }
 
 //==========================================================================================
//USER_COURSES comparison functions (needed to do a diff)
function ban_ez_users_courses_cmp($ban, $ez)
{
    $result=array();
    /*print "\ncomparing:\n";
    print_r($ban);
    print "\n-------\n";
    print_r($ez);
    print "\n============\n"; */
    if (strtolower($ban['MNEMONIQUE'])==strtolower($ez['course_code']) && strtolower($ban['netID'])==strtolower($ez['user_ID'])) {
        $result['key']=0; //same keys
        //print "same keys\n";
        if ($ez['origin']=="external") {
            $result['data']=0;
        } //same data
        else {
            $result['data']=1;
        } //not same data
    } elseif (strtolower($ban['MNEMONIQUE'])>strtolower($ez['course_code']) || (strtolower($ban['MNEMONIQUE'])==strtolower($ez['course_code']) && strtolower($ban['netID']) >strtolower($ez['user_ID']))) {
        $result['key']=1; //ban netid greater
             //print "key1 > key2\n";
    } else {
        $result['key']=2; //ban netid smaller
             //print "key1 < key2\n";
    }
    return $result;
}

function ez_users_courses_insert($ban)
{
    $sql="INSERT INTO ezcast_users_courses (`course_code`,`user_ID`,`origin`) VALUES ('{$ban['MNEMONIQUE']}','{$ban['netID']}','external'); ";
    return $sql;
}
function ez_users_courses_delete($ez)
{
    if ($ez['origin']=="external") {
        $sql="DELETE FROM ezcast_users_courses WHERE course_code='{$ez['course_code']}' AND user_ID='{$ez['user_ID']}';";
    } else {
        $sql="";
    }//keep internal user_courses
    return $sql;
}
 function ez_users_courses_update($ban)
 {
     $sql="UPDATE ezcast_users_courses SET   `origin`='external' WHERE course_code='{$ban['MNEMONIQUE']}' AND user_ID='{$ban['netID']}';";
     return $sql;
 }
 
 
 //==========================================================================================
//USER_COURSES comparison functions (needed to do a diff)
function ban_ban_users_courses_cmp($ban_courses, $ban_users)
{
    $result=array();
    /*print "\ncomparing:\n";
    print_r($ban);
    print "\n-------\n";
    print_r($ez);
    print "\n============\n"; */
    if (strtolower($ban_courses['MNEMONIQUE'])==strtolower($ban_users['MNEMONIQUE'])) {
        $result['key']=0; //same keys
      //print "same keys\n";
        $result['data']=0; //same data
    } elseif (strtolower($ban_courses['MNEMONIQUE']) > strtolower($ban_users['MNEMONIQUE'])) {
        $result['key']=1; //ban netid greater
             //print "key1 > key2\n";
    } else {
        $result['key']=2; //ban netid smaller
             //print "key1 < key2\n";
    }
    return $result;
}

function ban_users_courses_insert($ban)
{
    $sql="INSERT INTO   USERS  (`MNEMONIQUE`) VALUES ('{$ban['MNEMONIQUE']}'); ";
    return $sql;
}
function ban_users_courses_delete($ez)
{
    $sql="INSERT INTO  COURSES   WHERE course_code='{$ez['MNEMONIQUE']}';";
    return $sql;
}
 function ban_users_courses_update($ban)
 {
     $sql="UPDATE  SET   `origin`='external' WHERE course_code='{$ban['MNEMONIQUE']}';";
     return $sql;
 }
 
 
 /**
 * @desc check the differences between two flows given by ftnext1 ftnext2 customer functions and calls ftinsert ftdelete ftupdate accordingly. ftnext1 and ftnext2 must return values sorted according to the 'key'
 * @return void
 * @abstract ftcmp should return an assoc array with keys: ['key'] =0 if equals, 1 if 1 greater ("after" according to sort order) than 2, 2 if 2 greater than 1
 * @param function $ftnext1 (new data)
 * @param function $ftnext2 (old data)
 * @param function $ftcmp
 * @param function $ftinsert
 * @param function $ftdelete
 * @param function $ftupdate
 */

function diff($ftnext1, $ftnext2, $ftcmp, $ftinsert, $ftdelete, $ftupdate)
{
    $val1=$ftnext1();//new data
    $val2=$ftnext2();//old data
   
        $sql_cmd_array=array();
    
    while ($val1!==false && $val2!==false) {
        $cmp=$ftcmp($val1, $val2, $id_tmp);
        
        if ($cmp['key']==0) {
            //two elements key are equal so check the data go to next element in both file
            if ($cmp['data']!=0) {
                array_push($sql_cmd_array, $ftupdate($val1)); // keys are identicat but some data changed
            }
            //go to next records
            $val1=$ftnext1();
            $val2=$ftnext2();
        } elseif ($cmp['key']==1) {
            //val1 key > val2 key so an element of source2 (old) is not in source1 (new) -> its a delete
            array_push($sql_cmd_array, $ftdelete($val2));
            //go to next record in source2
            $val2=$ftnext2();
        } else {
            //$cmp['key']==2
            //val1 key < val2 key so an element of source1 (new) is not in source2 (old) -> its an insert
            array_push($sql_cmd_array, $ftinsert($val1));
            //go to next record in source2
            $val1=$ftnext1();
        }
    }//end while
    //now exhaust the remaining source and either call insert or delete
    while ($val1!==false) {
        array_push($sql_cmd_array, $ftinsert($val1));
        $val1=$ftnext1();
    }
    while ($val2!==false) {
        array_push($sql_cmd_array, $ftdelete($val2));
     
        $val2=$ftnext2();
    }
       
    return $sql_cmd_array;
}
/**
 * @desc gives next element of an array (this is not reentrant)
 * @staticvar int $idx
 * @staticvar array $in_array
 * @param type $array
 * @param type $reset
 * @return boolean
 */
function next1(&$array=array(), $reset=false)
{
    static $idx=0,$in_array=array();
    if ($reset) {
        $idx=0;
        $in_array=$array;
       
        return true;
    }
    if ($idx>=count($in_array)) {
        return false;
    }
    /*print "next1 returns:";
    print_r($in_array[$idx]);
    print"--------------------------\r";   */
    //print "next1:$idx\n";
    return $in_array[$idx++];
}
/**
 * @desc gives next element of an array (this is not reentrant)
 * @staticvar int $idx
 * @staticvar array $in_array
 * @param type $array
 * @param type $reset
 * @return boolean
 */
function next2(&$array=array(), $reset=false)
{
    static $idx=0,$in_array=array();
    if ($reset) {
        $idx=0;
        $in_array=$array;
        return true;
    }
    if ($idx>=count($in_array)) {
        return false;
    }
    /*print "next2 returns:";
    print_r($in_array[$idx]);
    print"--------------------------\r"; */
    //print "next2:$idx\n";
    return $in_array[$idx++];
}
