<?php

require_once __DIR__."/lib_various.php";
require_once __DIR__."/config.inc";

function debug($text)
{
   file_put_contents("/var/lib/ezcast/log/stream.log",$text . PHP_EOL, FILE_APPEND); 
}

// Start ExternalStreamDaemon if not already running
function ensure_external_stream_daemon_is_running($upload_root_dir) {
    debug("ensure1");
    if(ExternalStreamDaemon::is_running())
        return;
    debug("ensure2");
    // Start daemon in background
    $current_dir = __DIR__;
    system("php $current_dir/cli_external_stream_daemon.php $upload_root_dir > /dev/null &"); //checklater: php exec variable instead ?
    debug("ensure3");
}

/**
 * ... 
 * Daemon stops automatically after TIMEOUT_LENGHT
 */
class ExternalStreamDaemon {
    
   const TEMP_FOLDER = '/var/lib/ezcast/stream_var/';  
   //todo: make this relative to TEMP_FOLDER  
   const PID_FILE = '/var/lib/ezcast/stream_var/external_stream_daemon.pid';
   //lock file to be placed at stream root dir to prevent the daemon to copy files from it (content does not matter, only file existence is checked)
   const SYNC_LOCK_FILENAME = 'sync_lock';
   const STOP_FILE = '/var/lib/ezcast/stream_var/external_stream_stop';
   const READY_FILE = '/var/lib/ezcast/stream_var/external_stream_ready';
   const TIMEOUT_LENGHT = 43200; // in seconds, 12H
 
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
       $this->lock_file = $this->local_root_path . self::SYNC_LOCK_FILENAME;
       $this->start_time = time();
               
       $this->ssh_user = $streaming_video_alternate_server_user;
       $this->ssh_address = $streaming_video_alternate_server_address;
       $this->ssh_remote_root_path = $streaming_video_alternate_server_files_root_location;
     
       //todo: more sanity checks
       //extra checks
       if(!is_writable(dirname($this->lock_file)))
           throw new Exception('ExternalStreamDaemon:: lock file is not writable: ' . $this->lock_file);
       if(!is_writable(dirname(self::TEMP_FOLDER)))
           throw new Exception('ExternalStreamDaemon:: temp folder is not writable: ' . $this->lock_file);
   }
  
   static function is_running() {
       return is_process_running(get_pid_from_file(ExternalStreamDaemon::PID_FILE));
   }
 
   static function pause() {
       file_put_contents($this->lock_file, "1"); //content does not matter
   }
   
   static function resume() {
       if(file_exists($this->lock_file))
           unlink($this->lock_file);
   }
   
   function is_paused() {
       return file_exists($this->lock_file);
   }
   
   //return true if ready to stream
   static function is_ready() {
       return file_exists(self::READY_FILE) && self::is_running();
   }
   
   /* ready: true/false */
   function set_ready($ready) {
       if($ready == true)
           file_put_contents(self::READY_FILE, "1"); //content does not matter
       else if (file_exists(self::READY_FILE))
           unlink(self::READY_FILE);
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
       if(file_exists(self::STOP_FILE))
           unlink(self::STOP_FILE);
   }
   
   // true if daemon was started more than TIMEOUT_LENGHT seconds ago
   function is_in_timeout() {
       return $this->start_time + self::TIMEOUT_LENGHT < time();
   }
   
   function sync_files() {
	global $streaming_video_alternate_server_keyfile;
           // -- temp code for testing
	
	$command_key_part = "";
	if(isset($streaming_video_alternate_server_keyfile) and $streaming_video_alternate_server_keyfile != "")
	    $command_key_part = "-e 'ssh -i $streaming_video_alternate_server_keyfile' ";
 
	//copy m3u8 after so that we don't reference .ts not yet copied
	
       $temp_folder = self::TEMP_FOLDER . '/m3u8/';
       echo "copy m3u8" . PHP_EOL;
       echo system("rsync -rP --delete --exclude='*.ts' $this->local_root_path $temp_folder");
       echo "send ts" . PHP_EOL;
       echo "system(\"rsync -rP --delete --exclude='*.m3u8' $command_key_part $this->local_root_path $this->ssh_user@$this->ssh_address:$this->ssh_remote_root_path\")";
       echo system("rsync -rP --delete --exclude='*.m3u8' $command_key_part $this->local_root_path $this->ssh_user@$this->ssh_address:$this->ssh_remote_root_path");
       echo "Send m3u8" . PHP_EOL;
       echo system("rsync -rP $command_key_part $temp_folder $this->ssh_user@$this->ssh_address:$this->ssh_remote_root_path");
   }
   
   function run() {
       //make sure stop file from previous run does not exists anymore
       $this->delete_stop_file();
       $this->set_ready(false);
       $this->sync_files();
       $this->set_ready(true);
       
       while(true) {
           
           // Do nothing and wait if daemon is paused
           if($this->is_paused())
           {
               sleep(1);
               continue;
           }
           
           $time = time();
           $this->sync_files();
           sleep(1);
          
           $new_time = time();
           $diff = $new_time - $time;
           echo "Sync took $diff" . PHP_EOL;
           
           if($this->must_stop() || $this->is_in_timeout())
               break; //stop infinite loop
       }
       
       $this->set_ready(false);
   }
}
