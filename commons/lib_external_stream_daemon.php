<?php

// /!\ Check WHAT STILL NEEDS TO BE DONE below, this is not ready for production usage.

require_once __DIR__."/lib_various.php";
require_once __DIR__."/config.inc";

// Start ExternalStreamDaemon if not already running
function ensure_external_stream_daemon_is_running($upload_root_dir) {
    if(ExternalStreamDaemon::is_running())
        return;
    
    // Start daemon in background
    $current_dir = __DIR__;
    system("php $current_dir/cli_external_stream_daemon.php $upload_root_dir > /dev/null &"); //checklater: php exec variable instead ?
}

/**
 * 
 * *** Description
 * This class handle the syncing of stream video files from the ezmanager server to a remote distribution server.
 * This system is divided in two parts :
 *  - The actual files syncing, handled here and triggered in ezmanager - streaming_content_add()
 *  - The actual redirection of the users, handled in ezplayer - asset_streaming_player_update
 * 
 * *** Usage
 * - All local configuration is done in config.inc
 * - The remote server must have crossdomain enabled. With apache, this may be done by adding a crossdomain.xml file in the web space root folder (Google for more details)
 * - The local server must have an auto login with ssh access to the remote server
 * - Call ensure_external_stream_daemon_is_running to start the sync process
 *      - You may use cli_external_stream_daemon manually instead for debugging/testing purpose
 * - 
 *
 * *** Additional usage notes
 * This class in meant to be run in a separate process by starting cli_external_stream_daemon in background. For now, only one instance is meant to be run at a time.
 * You can check the state or interact with this object by calling the static functions such as is_running(), is_ready(), stop().
 * The daemon stops automatically after TIMEOUT_LENGHT
 * 
 * *** WHAT STILL NEEDS TO BE DONE
 * - sshd seems to block incoming connexions after a while (I guess after too much requests)
 * - code in streaming_content_add enabling the sync seems to cause an error if syncing is enabled (stream files are not even present localy anymore). Redirect seems fine though.
 * - the redirect currently only support "high" stream quality. See create_m3u8_external() function.
 * 
 * You can already use this class by using the cli_external_stream_daemon to sync files, and enable redirect only in config.inc.
 * 
 * *** What may be improved
 * - Make *_FILE variables relative to TEMP_FOLDER
 * - More sanity checks on construction (like checking if some configs are empty)
 * - This class currently allows to be run only one time. To change this:
 *      - State files location should be changed to a different folder per recording.
 *      - Some other idea must be found instead of static function for communicating with this process. Maybe just add the root video folder as argument to them would suffice.
 *      - (Maybe other things I don't see just now)
 * - Files synchronisation is currently done with rsync, which has several consequences :
 *      - Syncing get slightly slower over time, since rsync wtill check every existing files for size differences. This is still very fast and shouldn't cause problems except if you plan to stream for several years.
 *      - sshd seems to block incoming connexions after a while (I guess after too much requests). This still needs to be checked.
 *  
 */
class ExternalStreamDaemon {
    
   const TEMP_FOLDER = '/var/lib/ezcast/stream_var/';  
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
    
   /* Constructor. Multiple instances currently not supported.
    * @param string $upload_root_dir Source files directory
    */
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
     
       // extra sanity checks
       if(!is_writable(dirname($this->lock_file)))
           throw new Exception('ExternalStreamDaemon:: lock file is not writable: ' . $this->lock_file);
       if(!is_writable(dirname(self::TEMP_FOLDER)))
           throw new Exception('ExternalStreamDaemon:: temp folder is not writable: ' . $this->lock_file);
   }
  
   // return true if ExternalStreamDaemon is currently running
   static function is_running() {
       return is_process_running(get_pid_from_file(ExternalStreamDaemon::PID_FILE));
   }
 
   // return true if we've done at least one complete sync and redirect can be enabled
   static function is_ready() {
       return file_exists(self::READY_FILE) && self::is_running();
   }
   
   // create stop marker. Daemon will stop at the end of the current sync operation.
   static function stop() {
       file_put_contents(self::STOP_FILE, "1"); //content does not matter
   }
   
   // write the pid of the hosting process in PID_FILE (this is used by is_running).
   function writePID() {
        file_put_contents(self::PID_FILE, getmypid());
   }
   
   // pause syncing
   function pause() {
       file_put_contents($this->lock_file, "1"); //content does not matter
   }
   
   // resume syncing if paused
   function resume() {
       if(file_exists($this->lock_file))
           unlink($this->lock_file);
   }
   
   function is_paused() {
       return file_exists($this->lock_file);
   }
   
   /* See is_ready()
    * @param bool $ready
    * */
   function set_ready($ready) {
       if($ready == true)
           file_put_contents(self::READY_FILE, "1"); //content does not matter
       else if (file_exists(self::READY_FILE))
           unlink(self::READY_FILE);
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
	
	$command_key_part = "";
	if(isset($streaming_video_alternate_server_keyfile) and $streaming_video_alternate_server_keyfile != "")
	    $command_key_part = "-e 'ssh -i $streaming_video_alternate_server_keyfile' ";
 
       //first get the m3u8 but don't sync them yet, we need to send *.ts files first so that the m3u8 does not reference file not yet existing
       $temp_folder = self::TEMP_FOLDER . '/m3u8/';
       /* echo "copy m3u8" . PHP_EOL; */
       echo system("rsync -rP --delete --exclude='*.ts' $this->local_root_path $temp_folder");
       /* echo "send ts" . PHP_EOL; */
       //--size-only for speed up
       echo system("rsync -rP --delete --size-only --exclude='*.m3u8' $command_key_part $this->local_root_path $this->ssh_user@$this->ssh_address:$this->ssh_remote_root_path");
       /* echo "Send m3u8" . PHP_EOL; */
       echo system("rsync -rP $command_key_part $temp_folder $this->ssh_user@$this->ssh_address:$this->ssh_remote_root_path");
   }
   
   // main loop, sync files until told to stop (with stop() function)
   function run() {
       $this->WritePID();
       // make sure stop and ready files from previous run do not exist anymore
       $this->delete_stop_file();
       $this->set_ready(false);
       
       // first sync
       $this->sync_files();
       $this->set_ready(true);
       
       while(true) {
           if($this->is_paused()) { // do nothing and wait if daemon is paused
               sleep(1);
               continue;
           }
           
           $this->sync_files();
           sleep(1);
          
           // stop conditions
           if($this->must_stop() || $this->is_in_timeout())
               break;
       }
       
       // not necessary but let's clean after ourselves
       $this->delete_stop_file();
       $this->set_ready(false);
   }
}
