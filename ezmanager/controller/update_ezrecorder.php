<?php


function index($param = array())
{
    global $input;
    global $repository_path;
    global $basedir;
    require_once $basedir.'/commons/lib_sql_management.php';
//get the album list
$album= acl_authorized_albums_list_created($assoc = false);
$modif=false;
foreach ($album as $album_name) {
   $course_info=db_course_read($album_name);
   $in_recorders=isset($course_info['in_recorders']) && $course_info['in_recorders']!=false;
   $input_in_recorders=isset($input["recorder_access"][$album_name]) && $input["recorder_access"][$album_name]=="on";
   //check box is ticked. Was it ticked before?  
   if  ($input_in_recorders xor $in_recorders){
     //the value has been changed so record it
     $res=db_in_recorder_update($album_name,$input_in_recorders); 
     $modif=true;
    }       
}

$ezrecorder_pw=$input['ezrecorder_pw'];
if($ezrecorder_pw!="" AND $ezrecorder_pw!="PASSWORD"){
  //password changed
  if(!db_user_read($_SESSION['user_login'])){
    //user doesn't exist yet, lets create it
    //fetch name
    $full_name=$_SESSION['user_full_name'];
    $first_space=strpos($full_name,' ');
    if($first_space===false){
      //no space, so make sure that we have the full info
      $forename=$full_name;
      $surname=$full_name;
    }
    else{
    //the space is the separator between fore and last name
    $forename=  substr($full_name,0,$first_space);
    $surname=trim(substr($full_name,$first_space+1));
    }
    //print "cmd:db_user_create(".$_SESSION['user_login'].", $surname, $forename, ".$ezrecorder_pw.", 0)";
    $res=  db_user_create($_SESSION['user_login'], $surname, $forename, $ezrecorder_pw, 0);
  }
  else{
  //user already exists
  $res=db_user_set_recorder_passwd($_SESSION['user_login'], $ezrecorder_pw);
  }  
  $modif=true;
}
if($modif){
    require_once(__DIR__.'/../../ezadmin/lib_push_changes.php');   
    notify_changes(true);//set flag file to tell cli_push_changes.php that it needs to push changes to recorders.
}
    if (isset($res) && !$res) {
        error_print_message("Erreur lors de l'écriture des données");
        die;
    }

    //  view_main();
    require_once template_getpath('popup_ezrecorder_successfully_updated.php');
}
