<?php

include_once __DIR__."lib_various.php";
include_once __DIR__."config.inc";

// Start ExternalStreamDaemon if not already running
function ensure_external_stream_daemon_is_running($upload_root_dir) {
    global $pid_file;
    
    if(is_process_running($pid_file))
        return;
    
    // Start daemon in background
    $current_dir = __DIR__;
    system("php $current_dir/cli_external_stream_daemon.php $upload_root_dir > /dev/null &"); //checklater: php exec variable instead ?
}

/**
 * ... 
 * Daemon stops automatically after TIMEOUT_LENGHT
 */
class ExternalStreamDaemon {
    
   const PID_FILE = '/var/lib/ezcast/external_stream_daemon.pid';
   //lock file to be placed at stream root dir to prevent the daemon to copy files from it (content does not matter, only file existence is checked)
   const SYNC_LOCK_FILENAME = 'sync_lock';
   const STOP_FILE = '/var/lib/ezcast/external_stream_stop';
   const TIMEOUT_LENGHT = 86400; // in seconds, 24H
   
   var $ssh_user;
   var $ssh_address;
   var $ssh_remote_root_path;
   var $local_root_path;
   var $lock_file;
   var $stop_file;
   var $start_time;
    
   function __construct($upload_root_dir) {
       global $streaming_video_alternate_server_user;
       global $streaming_video_alternate_server_address;
       global $streaming_video_alternate_server_files_root_location;
       
       $this->local_root_path = $upload_root_dir . '/';
       $this->lock_file = local_root_path . self::SYNC_LOCK_FILENAME;
       $this->start_time = time();
               
       $this->ssh_user = $streaming_video_alternate_server_user;
       $this->ssh_address = $streaming_video_alternate_server_address;
       $this->ssh_remote_root_path = $streaming_video_alternate_server_files_root_location;
       
       //extra checks
       if(!is_writable(self::PID_FILE))
           throw new Exception('ExternalStreamDaemon:: pid file is not writable: ' . self::PID_FILE);
       if(!is_writable(self::STOP_FILE))
           throw new Exception('ExternalStreamDaemon:: stop file is not writable: ' . self::STOP_FILE);
       if(!is_writable($this->lock_file))
           throw new Exception('ExternalStreamDaemon:: lock file is not writable: ' . $this->lock_file);
   }
   
   static function pause() {
       file_put_contents($this->lock_file, "1"); //content does not matter
   }
   
   static function resume() {
       unlink($this->lock_file);
   }
   
   static function is_paused() {
       return file_exists($this->lock_file);
   }
   
   // create stop marker. Daemon will stop at the end of the current sync operation.
   static function stop() {
       file_put_contents(self::STOP_FILE, "1"); //content does not matter
   }
   
   // true if stop marker is present
   function must_stop() {
       return file_exists(self::STOP_FILE);
   }
   
   // delete stop marker
   function delete_stop_file() {
       unlink(self::STOP_FILE);
   }
   
   // true if daemon was started more than TIMEOUT_LENGHT seconds ago
   function is_in_timout() {
       return $this->start_time + TIMEOUT_LENGHT < time();
   }
   
   function run() {
       //make sure stop file from previous run does not exists anymore
       delete_stop_file();
       
       while(true) {
           
           // Do nothing and wait if daemon is paused
           if(is_paused())
           {
               sleep(1);
               continue;
           }
           
           // -- temp code for testing
           system("rsync -ru . $this->ssh_user@$this->ssh_address:$this->ssh_root_path");
           sleep(1);
           // --
           
           if(must_stop() || is_in_timout())
               break; //stop infinite loop
       }
       
   }
}