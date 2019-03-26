<?php
/*
    script used to launch command line in each recorder
    If no param => just ping each IP to check if it respond
*/
require_once("./commons/lib_sql_management.php");
require_once("./commons/config");

//get list of recorder from DB
$classList=db_classrooms_list();

//make action for each recorder
for($v=0;$v<count($classList);$v++){
    //ping to test if recorder is online
    exec("ping -c 1 -W 1 " . $classList[$v]['IP'], $output, $result);
    //Cant ping => write message in red
    if ($result != 0){
       echo "\033[31m ".($v+1)."/".count($classList)." Cant ping ".$classList[$v]['IP']."   \n";
        echo "\033[0m";
    }     
//    ping success => execute commands
    else{
        echo "\033[32m ".($v+1)."/".count($classList)." Ping ".$classList[$v]['IP']."  SUCCESS \n";
        echo "\033[0m";
        //execute each command one by one
        for($i=1;$i<count($argv);$i++){
            $cmd="sudo -su www-data ssh $recorder_user@".$classList[$v]['IP']." '".$argv[$i]."'";       
            exec($cmd, $output, $result);
            if ($result != 0){
                echo "\033[31m ".$cmd."   \n";
                echo "\033[0m";
            } 
            else{
                echo "\033[32m ".$cmd." \n";
                echo "\033[0m";
            }
        }
    }
}

?>