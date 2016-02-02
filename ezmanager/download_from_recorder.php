<?php
//is this still used ?

//This program receives queries from classroom recording agents to tell that a new recorging is ready for download
//IP adresses of machines allowed to submit is store in a file
include_once "config.inc";
include_once "classroom_recorder_ip.inc"; //valid ip file

$input=array_merge($_GET,$_POST);

//look for caller's ip in config files
$caller_ip=trim($_SERVER["REMOTE_ADDR"]);

$key=array_search($caller_ip, $podcv_ip);
if($key===false){
    //ip not found
    print "not talking to you";
    die;
}
//deduce podcs ip:
$cur_podcs_ip=$podcs_ip[$key];
$cur_podcv_ip=$caller_ip;
//get input parameters
$record_type=$input['record_type'];// cam|slide|camslide
$record_date=$input['record_date'];
$course_name=$input['course_name'];
$record_name_sanitized=str_to_safedir($record_date."_".$course_name);
$request_date=date($dir_date_format);
//creates a directory that will contain slide, camera and record metadata
$record_dir=$recorder_upload_dir."/".$record_name_sanitized;
if(!file_exists($record_dir))  mkdir($record_dir);

//now we need to call the recording download cli program (outside of web environment execution to avoid timeout
//this process will contact podcv and podcs and ssh+rsync download cam.mov, slide.mov & _metadata.xml
$downloadxml="<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone='yes'?>
<download_metadata>
<request_date>$request_date</request_date>
<course_name>$course_name</course_name>
<record_date>$record_date</record_date>
<download_complete>false</download_complete>
<record_type>$record_type</record_type>
<podcv_ip>$cur_podcv_ip</podcv_ip>
<podcs_ip>$cur_podcs_ip</podcs_ip>
</download_metadata>
";

file_put_contents($record_dir."/download_data.xml", $downloadxml);
$cmd= "nohup nice -n 14 $php_cli_cmd $recorder_download_pgm $record_name_sanitized >>$record_dir/download.log 2>&1";
$pid=shell_exec($cmd);
//print "will execute command: '$cmd'\n<br>";
print "OK:$pid";


/**
 *
 * @param string $string
 * @return string
 * @desc returns a directory safe
 */
function str_to_safedir($string){
  $toalnum="";
  for($idx=0;$idx<strlen($string);$idx++)
    if(ctype_alnum($string[$idx]) || $string[$idx]=="-")
     $toalnum.=$string[$idx];
     else
     $toalnum.="_";
  return $toalnum;
}
