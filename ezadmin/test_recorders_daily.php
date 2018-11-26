<?php

// List all classrooms and tries to ssh connect to each. Print results and send a report mail 
//

require_once("/usr/local/ezcast/commons/lib_sql_management.php");
require_once("/usr/local/ezcast/commons/config.inc");
include_once 'config.inc';
include_once 'lib_various.php';

global $adress_mail_information_recorder;

//get list of recorder from DB
$check_list=db_classrooms_list_enabled();
$result_str = "SSH connects results: <br/><br/>".PHP_EOL.PHP_EOL;

foreach($check_list as $to_check) {
//    echo(json_encode($to_check));
  $ip = $to_check["IP"];
  $name = $to_check["name"];
 
  echo "CHECKING $ip : $name" . PHP_EOL;
  $return_val = 0;
  system("ssh -o ConnectTimeout=2 -o BatchMode=yes ezrecorder@$ip \"touch /dev/null\" ", $return_val);
 
      
        
  $result_str .= "$ip | $name" . PHP_EOL . "<br/> => ";
  if($return_val == 0) {
    $result_str .= "<font color=\"green\">OK</font>". PHP_EOL . "<br/>";  
    $df=getSpaceUsed($ip);
    if($df>80)
        $result_str .=  "=> <font color=\"red\">HArd drive almost Full ( ".getSpaceUsed($ip)."%)</font>". PHP_EOL . "<br/>";  
    else     
        $result_str .= "=> <font color=\"green\">Hard drive : ".getSpaceUsed($ip)."%</font>". PHP_EOL . "<br/>";

  } else {  
    $result_str .= "<font color=\"red\">FAILED</font>". PHP_EOL. "<br/>";
  }
  $result_str .= PHP_EOL . "<br/>";   
  
   
       
}

echo $result_str;

$mail_str = "( echo 'To: ".$adress_mail_information_recorder." ; echo 'Subject: Classrooms availability check'; echo 'Content-Type: text/html'; echo 'MIME-Version: 1.0'; echo ''; echo '$result_str'; )";
system("$mail_str | /usr/sbin/sendmail -t -f ".$mailto_alert);
