<?php
require_once __DIR__.'/../commons/config.inc';
require_once(__DIR__.'/lib_push_changes.php');
if($argc==2 && $argv[1]=='--force')
  $force=true;
 else
  $force=false;
 if($argc==2 && ($argv[1]=='--help' || $argv[1]=='-h')){
   print "usage: ".$argv[0]." [--force] push admins, users, passwords, courses changes to recorders and to ezmanager\n";  
   die;
 }
 
if(notify_changes_isset() || $force){
  notify_changes(false);
  echo "Pushing..." . PHP_EOL;
  $failed_cmd = push_changes();
  var_dump($failed_cmd);
  echo "Finished" . PHP_EOL;
}
