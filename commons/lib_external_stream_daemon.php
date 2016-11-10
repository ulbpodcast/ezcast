<?php

// /!\ Check WHAT STILL NEEDS TO BE DONE below, this is not ready for production usage.

require_once __DIR__."/lib_various.php";
require_once __DIR__."/config.inc";

// Start ExternalStreamDaemon if not already running
function ensure_external_stream_daemon_is_running($video_root_dir, $asset_token) {
    global $php_cli_cmd;
    if(ExternalStreamDaemon::is_running($asset_token))
        return;
   
    // Start daemon in background
    $command = $php_cli_cmd . ' ' .__DIR__."/cli_external_stream_daemon.php $video_root_dir $asset_token > /dev/null &";
    //file_put_contents("/var/lib/ezcast/external_stream/FOMXFAVE/sync_command", $command . PHP_EOL);
    system($command); 
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
 * - replace rsync with ssh2
 * - the redirect currently only support "high" stream quality. See create_m3u8_external() function.
 * - vm call stubs (ip, location de la clé, user -> fourni par le cloud_vm_start)
 * - EZPlayer in browser currently wont switch ask for a new live.m3u8 and so switch is not automatical for client already connected 
 * 
 * You can already use this class by using the cli_external_stream_daemon to sync files, and enable redirect only in config.inc.
 *  
 */

class ExternalStreamDaemon {
    
   //lock file to be placed at stream root dir to prevent the daemon to copy files from it (content does not matter, only file existence is checked)
   const TIMEOUT_LENGHT = 43200; // in seconds, 12H
 
   var $ssh_user;
   var $ssh_address;
   var $ssh_remote_root_path;
   var $local_root_path;
   var $asset_token;
   var $stop_file;
   var $start_time;
    
   /* Constructor. Multiple instances currently not supported.
    * @param string $video_root_dir Source files directory
    */
   function __construct($video_root_dir, $asset_token) {
       global $streaming_video_alternate_server_user;
       global $streaming_video_alternate_server_address;
       global $streaming_video_alternate_server_document_root;
       
       $this->local_root_path = $video_root_dir . '/';
       $this->asset_token = $asset_token;
       $this->start_time = time();
               
       $this->ssh_user = $streaming_video_alternate_server_user;
       $this->ssh_address = $streaming_video_alternate_server_address;
       $this->ssh_remote_root_path = $streaming_video_alternate_server_document_root . '/' . $asset_token;
     
       // extra sanity checks
       $temp_folder = $this->get_temp_folder($this->asset_token);
       if(!is_writable(dirname($temp_folder)))
           trigger_error('ExternalStreamDaemon:: temp folder is not writable: ' . $temp_folder, E_USER_ERROR);
       if($this->asset_token == "")
           trigger_error('ExternalStreamDaemon:: no token given', E_USER_ERROR);
       if($this->local_root_path == "")
           trigger_error('ExternalStreamDaemon:: no video path given', E_USER_ERROR);
   }

   static function get_temp_folder($asset_token) {

       global $repository_basedir;
       $temp_folder = $repository_basedir . '/external_stream/' . $asset_token . '/';
       return $temp_folder;
   }

   static function get_pid_file_path($asset_token) {
	
	return self::get_temp_folder($asset_token) . '/pid';
   }

   private static function get_lock_file_path($asset_token) {
	return self::get_temp_folder($asset_token) . '/lock';
   }

   private static function get_ready_file_path($asset_token) {
	return self::get_temp_folder($asset_token) . '/ready';
   }

   private static function get_stop_file_path($asset_token) {
	return self::get_temp_folder($asset_token) . '/stop';
   }

   // return true if ExternalStreamDaemon is currently running
   static function is_running($asset_token) {
       return is_process_running(get_pid_from_file(self::get_pid_file_path($asset_token)));
   }
 
   // return true if we've done at least one complete sync and redirect can be enabled
   static function is_ready($asset_token) {
       $ready_file = self::get_ready_file_path($asset_token);
       return file_exists($ready_file) && self::is_running($asset_token);
   }
   
   // create stop marker. Daemon will stop at the end of the current sync operation.
   static function stop($asset_token) {
       $stop_file = self::get_stop_file_path($asset_token);
       if(!file_exists(dirname($stop_file)))
           mkdir(dirname($stop_file), 0777, true); 
       file_put_contents($stop_file, "1"); //content does not matter
   }
   
   // write the pid of the hosting process (this is used by is_running).
   private function write_PID() {
       $pid_file = self::get_pid_file_path($this->asset_token);
       if(!file_exists(dirname($pid_file)))
           mkdir(dirname($pid_file), 0777, true);
        file_put_contents($pid_file, getmypid());
   }
   
   // pause syncing
   static function pause($asset_token) {
       $lock_file = self::get_lock_file_path($asset_token);
       if(!file_exists(dirname($lock_file)))
           mkdir(dirname($lock_file), 0777, true);
       file_put_contents($lock_file,"1"); //content does not matter
   }
   
   // resume syncing if paused
   static function resume($asset_token) {
       $lock_file = self::get_lock_file_path($asset_token);
       if(file_exists($lock_file))
           unlink($lock_file);
   }
   
   static function is_paused($asset_token) {
       $lock_file = self::get_lock_file_path($asset_token);
       return file_exists($lock_file);
   }
   
   /* See is_ready()
    * @param bool $ready
    * */
   private function set_ready($ready) {
       $ready_file = self::get_ready_file_path($this->asset_token);
       if($ready == true)
           file_put_contents($ready_file, "1"); //content does not matter
       else if (file_exists($ready_file))
           unlink($ready_file);
   }
   
   // true if stop marker is present
   private function must_stop() {
       $stop_file = self::get_stop_file_path($this->asset_token);
       return file_exists($stop_file);
   }
   
   // delete stop marker
   private function delete_stop_file() {
       $stop_file = self::get_stop_file_path($this->asset_token);
       if(file_exists($stop_file))
           unlink($stop_file);
   }
   
   // true if daemon was started more than TIMEOUT_LENGHT seconds ago
   private function is_in_timeout() {
       return $this->start_time + self::TIMEOUT_LENGHT < time();
   }
   
   private function sync_files() {
	global $streaming_video_alternate_server_keyfile;
	
	$command_key_part = "";
	if(isset($streaming_video_alternate_server_keyfile) and $streaming_video_alternate_server_keyfile != "")
	    $command_key_part = "-e 'ssh -i $streaming_video_alternate_server_keyfile' ";
 
       //first get the m3u8 but don't sync them yet, we need to send *.ts files first so that the m3u8 does not reference file not yet existing
       
       $temp_folder = self::get_temp_folder($this->asset_token) . '/m3u8/';
       unlink($temp_folder);
       /* echo "copy m3u8" . PHP_EOL; */
       echo system("rsync -rP --delete --exclude='*.ts' $this->local_root_path $temp_folder");
       /* echo "send ts" . PHP_EOL; */
       //--size-only for speed up
       echo system("rsync -rP --delete --size-only --exclude='*.m3u8' $command_key_part $this->local_root_path $this->ssh_user@$this->ssh_address:$this->ssh_remote_root_path");
       /* echo "Send m3u8" . PHP_EOL; */
       echo system("rsync -rP $command_key_part $temp_folder $this->ssh_user@$this->ssh_address:$this->ssh_remote_root_path");
   }
   
   // main loop, sync files until told to stop (with stop() function)
   public function run() {
       $this->Write_PID();
       // make sure stop and ready files from previous run do not exist anymore
       $this->delete_stop_file();
       $this->set_ready(false);
       
       // first sync
       $this->sync_files();
       $this->set_ready(true);
       
       while(true) {
           if(self::is_paused($this->asset_token)) { // do nothing and wait if daemon is paused
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
